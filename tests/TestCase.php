<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\RequestHandlers\GedcomLoad;
use Fisharebest\Webtrees\Http\Routes\WebRoutes;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\MigrationService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\TreeService;
use Illuminate\Database\Capsule\Manager as DB;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;

use function app;
use function basename;
use function filesize;
use function http_build_query;
use function implode;
use function preg_match;
use function str_starts_with;
use function strcspn;
use function strlen;
use function strpos;
use function substr;

use const UPLOAD_ERR_OK;

/**
 * Base class for unit tests
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    public static ?object $mock_functions = null;

    protected static bool $uses_database = false;

    /**
     * Things to run once, before all the tests.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $webtrees = new Webtrees();
        $webtrees->bootstrap();

        // PSR7 messages and PSR17 message-factories
        Webtrees::set(ResponseFactoryInterface::class, Psr17Factory::class);
        Webtrees::set(ServerRequestFactoryInterface::class, Psr17Factory::class);
        Webtrees::set(StreamFactoryInterface::class, Psr17Factory::class);
        Webtrees::set(UploadedFileFactoryInterface::class, Psr17Factory::class);
        Webtrees::set(UriFactoryInterface::class, Psr17Factory::class);

        // This is normally set in middleware.
        Webtrees::set(ModuleThemeInterface::class, WebtreesTheme::class);

        // Need the routing table, to generate URLs.
        $router_container = new RouterContainer('/');
        (new WebRoutes())->load($router_container->getMap());
        Webtrees::set(RouterContainer::class, $router_container);

        I18N::init('en-US', true);

        if (static::$uses_database) {
            static::createTestDatabase();

            // This is normally set in middleware.
            (new Gedcom())->registerTags(Registry::elementFactory(), true);

            // Boot modules
            (new ModuleService())->bootModules(new WebtreesTheme());
        }
    }

    /**
     * Things to run once, AFTER all the tests.
     */
    public static function tearDownAfterClass(): void
    {
        if (static::$uses_database) {
            $pdo = DB::connection()->getPdo();
            unset($pdo);
        }

        parent::tearDownAfterClass();
    }

    /**
     * Create an SQLite in-memory database for testing
     */
    protected static function createTestDatabase(): void
    {
        $capsule = new DB();
        $capsule->addConnection([
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);
        $capsule->setAsGlobal();

        // Migrations create logs, which requires an IP address, which requires a request
        self::createRequest();

        // Create tables
        $migration_service = new MigrationService();
        $migration_service->updateSchema('\Fisharebest\Webtrees\Schema', 'WT_SCHEMA_VERSION', Webtrees::SCHEMA_VERSION);

        // Create config data
        $migration_service->seedDatabase();
    }

    /**
     * Create a request and bind it into the container.
     *
     * @param string                       $method
     * @param array<string>                $query
     * @param array<string>                $params
     * @param array<UploadedFileInterface> $files
     * @param array<string>                $attributes
     *
     * @return ServerRequestInterface
     */
    protected static function createRequest(
        string $method = RequestMethodInterface::METHOD_GET,
        array $query = [],
        array $params = [],
        array $files = [],
        array $attributes = []
    ): ServerRequestInterface {
        /** @var ServerRequestFactoryInterface */
        $server_request_factory = app(ServerRequestFactoryInterface::class);

        $uri = 'https://webtrees.test/index.php?' . http_build_query($query);

        $request = $server_request_factory
            ->createServerRequest($method, $uri)
            ->withQueryParams($query)
            ->withParsedBody($params)
            ->withUploadedFiles($files)
            ->withAttribute('base_url', 'https://webtrees.test')
            ->withAttribute('client-ip', '127.0.0.1')
            ->withAttribute('user', new GuestUser())
            ->withAttribute('route', new Route());

        foreach ($attributes as $key => $value) {
            $request = $request->withAttribute($key, $value);

            if ($key === 'tree') {
                app()->instance(Tree::class, $value);
            }
        }

        app()->instance(ServerRequestInterface::class, $request);

        return $request;
    }

    /**
     * Things to run before every test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (static::$uses_database) {
            DB::connection()->beginTransaction();
        }
    }

    /**
     * Things to run after every test
     */
    protected function tearDown(): void
    {
        if (static::$uses_database) {
            DB::connection()->rollBack();
        }

        Site::$preferences = [];

        Auth::logout();
    }

    /**
     * Import a GEDCOM file into the test database.
     *
     * @param string $gedcom_file
     *
     * @return Tree
     */
    protected function importTree(string $gedcom_file): Tree
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree         = $tree_service->create(basename($gedcom_file), basename($gedcom_file));
        $stream       = app(StreamFactoryInterface::class)->createStreamFromFile(__DIR__ . '/data/' . $gedcom_file);

        $tree_service->importGedcomFile($tree, $stream, $gedcom_file, '');

        $timeout_service = new TimeoutService();
        $controller      = new GedcomLoad($gedcom_import_service, $timeout_service);
        $request         = self::createRequest()->withAttribute('tree', $tree);

        do {
            $controller->handle($request);

            $imported = $tree->getPreference('imported');
        } while (!$imported);

        return $tree;
    }

    /**
     * Create an uploaded file for a request.
     *
     * @param string $filename
     * @param string $mime_type
     *
     * @return UploadedFileInterface
     */
    protected function createUploadedFile(string $filename, string $mime_type): UploadedFileInterface
    {
        /** @var StreamFactoryInterface */
        $stream_factory = app(StreamFactoryInterface::class);

        /** @var UploadedFileFactoryInterface */
        $uploaded_file_factory = app(UploadedFileFactoryInterface::class);

        $stream      = $stream_factory->createStreamFromFile($filename);
        $size        = filesize($filename);
        $status      = UPLOAD_ERR_OK;
        $client_name = basename($filename);

        return $uploaded_file_factory->createUploadedFile($stream, $size, $status, $client_name, $mime_type);
    }

    /**
     * Assert that a response contains valid HTML - either a full page or a fragment.
     *
     * @param ResponseInterface $response
     */
    protected function validateHtmlResponse(ResponseInterface $response): void
    {
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        self::assertEquals('text/html; charset=UTF-8', $response->getHeaderLine('content-type'));

        $html = $response->getBody()->getContents();

        self::assertStringStartsWith('<DOCTYPE html>', $html);

        $this->validateHtml(substr($html, strlen('<DOCTYPE html>')));
    }

    /**
     * Assert that a response contains valid HTML - either a full page or a fragment.
     *
     * @param string $html
     */
    protected function validateHtml(string $html): void
    {
        $stack = [];

        do {
            $html = substr($html, strcspn($html, '<>'));

            if (str_starts_with($html, '>')) {
                static::fail('Unescaped > found in HTML');
            }

            if (str_starts_with($html, '<')) {
                if (preg_match('~^</([a-z]+)>~', $html, $match)) {
                    if ($match[1] !== array_pop($stack)) {
                        static::fail('Closing tag matches nothing: ' . $match[0] . ' at ' . implode(':', $stack));
                    }
                    $html = substr($html, strlen($match[0]));
                } elseif (preg_match('~^<([a-z]+)(?:\s+[a-z_\-]+="[^">]*")*\s*(/?)>~', $html, $match)) {
                    $tag = $match[1];
                    $self_closing = $match[2] === '/';

                    $message = 'Tag ' . $tag . ' is not allowed at ' . implode(':', $stack) . '.';

                    switch ($tag) {
                        case 'html':
                            static::assertSame([], $stack);
                            break;
                        case 'head':
                        case 'body':
                            static::assertSame(['head'], $stack);
                            break;
                        case 'div':
                            static::assertNotContains('span', $stack, $message);
                            break;
                    }

                    if (!$self_closing) {
                        $stack[] = $tag;
                    }

                    if ($tag === 'script' && !$self_closing) {
                        $html = substr($html, strpos($html, '</script>'));
                    } else {
                        $html = substr($html, strlen($match[0]));
                    }
                } else {
                    static::fail('Unrecognised tag: ' . substr($html, 0, 40));
                }
            }
        } while ($html !== '');

        static::assertSame([], $stack);
    }
}
