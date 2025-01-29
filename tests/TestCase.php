<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use PHPUnit\Framework\Constraint\Callback;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;

use function array_shift;
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
     * Create an SQLite in-memory database for testing
     */
    private static function createTestDatabase(): void
    {
        $capsule = new DB();
        $capsule->addConnection([
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);
        $capsule->setAsGlobal();

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
     * @param array<string|Tree>           $attributes
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
        $server_request_factory = Webtrees::make(ServerRequestFactoryInterface::class);

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
                Webtrees::set(Tree::class, $value);
            }
        }

        Webtrees::set(ServerRequestInterface::class, $request);

        return $request;
    }

    /**
     * Things to run before every test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $webtrees = new Webtrees();
        $webtrees->bootstrap();

        // PSR7 messages and PSR17 message-factories
        Webtrees::set(ResponseFactoryInterface::class, Psr17Factory::class);
        Webtrees::set(ServerRequestFactoryInterface::class, Psr17Factory::class);
        Webtrees::set(StreamFactoryInterface::class, Psr17Factory::class);
        Webtrees::set(UploadedFileFactoryInterface::class, Psr17Factory::class);
        Webtrees::set(UriFactoryInterface::class, Psr17Factory::class);

        // This is normally set in middleware.
        Webtrees::set(ModuleThemeInterface::class, new WebtreesTheme());

        // Need the routing table, to generate URLs.
        $router_container = new RouterContainer('/');
        (new WebRoutes())->load($router_container->getMap());
        Webtrees::set(RouterContainer::class, $router_container);
        $module_service = new ModuleService();
        Webtrees::set(ModuleService::class, $module_service);

        if (static::$uses_database) {
            self::createTestDatabase();

            // This is normally set in middleware.
            (new Gedcom())->registerTags(Registry::elementFactory(), true);

            // Boot modules
            $module_service->bootModules(new WebtreesTheme());

            I18N::init('en-US');
        } else {
            I18N::init('en-US', true);
        }

        self::createRequest();
    }

    /**
     * Things to run after every test
     */
    protected function tearDown(): void
    {
        if (static::$uses_database) {
            DB::connection()->disconnect();
        }

        Session::clear(); // Session data is stored in the super-global
        Site::$preferences = []; // These are cached from the database
    }

    protected function importTree(string $gedcom_file): Tree
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create(basename($gedcom_file), basename($gedcom_file));
        $stream                = Webtrees::make(StreamFactoryInterface::class)->createStreamFromFile(__DIR__ . '/data/' . $gedcom_file);

        $tree_service->importGedcomFile($tree, $stream, $gedcom_file, '');

        $timeout_service = new TimeoutService();
        $controller      = new GedcomLoad($gedcom_import_service, $timeout_service);
        $request         = self::createRequest()->withAttribute('tree', $tree);

        do {
            $controller->handle($request);
        } while ($tree->getPreference('imported') !== '1');

        return $tree;
    }

    protected function createUploadedFile(string $filename, string $mime_type): UploadedFileInterface
    {
        $stream_factory        = Webtrees::make(StreamFactoryInterface::class);
        $uploaded_file_factory = Webtrees::make(UploadedFileFactoryInterface::class);

        $stream      = $stream_factory->createStreamFromFile($filename);
        $size        = filesize($filename);
        $status      = UPLOAD_ERR_OK;
        $client_name = basename($filename);

        self::assertIsInt($size);

        return $uploaded_file_factory->createUploadedFile($stream, $size, $status, $client_name, $mime_type);
    }

    protected function validateHtmlResponse(ResponseInterface $response): void
    {
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        self::assertEquals('text/html; charset=UTF-8', $response->getHeaderLine('content-type'));

        $html = $response->getBody()->getContents();

        self::assertStringStartsWith('<DOCTYPE html>', $html);

        $this->validateHtml(substr($html, strlen('<DOCTYPE html>')));
    }

    protected function validateHtml(string $html): void
    {
        $stack = [];

        do {
            $html = substr($html, strcspn($html, '<>'));

            if (str_starts_with($html, '>')) {
                static::fail('Unescaped > found in HTML');
            }

            if (str_starts_with($html, '<')) {
                if (preg_match('~^</([a-z]+)>~', $html, $match) === 1) {
                    if ($match[1] !== array_pop($stack)) {
                        static::fail('Closing tag matches nothing: ' . $match[0] . ' at ' . implode(':', $stack));
                    }
                    $html = substr($html, strlen($match[0]));
                } elseif (preg_match('~^<([a-z]+)(?:\s+[a-z_\-]+="[^">]*")*\s*(/?)>~', $html, $match) === 1) {
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

    /**
     * Workaround for removal of withConsecutive in phpunit 10.
     *
     * @param array<int,mixed> $parameters
     */
    protected static function withConsecutive(array $parameters): Callback
    {
        return self::callback(static function ($parameter) use ($parameters): bool {
            static $array = null;

            $array ??= $parameters;

            return $parameter === array_shift($array);
        });
    }
}
