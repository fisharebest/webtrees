<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Webtrees\Contracts\CacheFactoryInterface;
use Fisharebest\Webtrees\Factories\CacheFactory;
use Fisharebest\Webtrees\Factories\FamilyFactory;
use Fisharebest\Webtrees\Factories\FilesystemFactory;
use Fisharebest\Webtrees\Factories\GedcomRecordFactory;
use Fisharebest\Webtrees\Factories\HeaderFactory;
use Fisharebest\Webtrees\Factories\IndividualFactory;
use Fisharebest\Webtrees\Factories\LocationFactory;
use Fisharebest\Webtrees\Factories\MediaFactory;
use Fisharebest\Webtrees\Factories\NoteFactory;
use Fisharebest\Webtrees\Factories\RepositoryFactory;
use Fisharebest\Webtrees\Factories\SourceFactory;
use Fisharebest\Webtrees\Factories\SubmissionFactory;
use Fisharebest\Webtrees\Factories\SubmitterFactory;
use Fisharebest\Webtrees\Factories\XrefFactory;
use Fisharebest\Webtrees\Http\RequestHandlers\GedcomLoad;
use Fisharebest\Webtrees\Http\Routes\WebRoutes;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Services\MigrationService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\TreeService;
use Illuminate\Database\Capsule\Manager as DB;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\Cache\Adapter\NullAdapter;

use function app;
use function basename;
use function filesize;
use function http_build_query;
use function microtime;

use const UPLOAD_ERR_OK;

/**
 * Base class for unit tests
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var object */
    public static $mock_functions;
    /** @var bool */
    protected static $uses_database = false;

    /**
     * Things to run once, before all the tests.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // Use nyholm as our PSR7 factory
        app()->bind(ResponseFactoryInterface::class, Psr17Factory::class);
        app()->bind(ServerRequestFactoryInterface::class, Psr17Factory::class);
        app()->bind(StreamFactoryInterface::class, Psr17Factory::class);
        app()->bind(UploadedFileFactoryInterface::class, Psr17Factory::class);
        app()->bind(UriFactoryInterface::class, Psr17Factory::class);

        // Register the factories
        Registry::cache(new CacheFactory());
        Registry::familyFactory(new FamilyFactory());
        Registry::filesystem(new FilesystemFactory());
        Registry::gedcomRecordFactory(new GedcomRecordFactory());
        Registry::headerFactory(new HeaderFactory());
        Registry::individualFactory(new IndividualFactory());
        Registry::locationFactory(new LocationFactory());
        Registry::mediaFactory(new MediaFactory());
        Registry::noteFactory(new NoteFactory());
        Registry::repositoryFactory(new RepositoryFactory());
        Registry::sourceFactory(new SourceFactory());
        Registry::submissionFactory(new SubmissionFactory());
        Registry::submitterFactory(new SubmitterFactory());
        Registry::xrefFactory(new XrefFactory());

        app()->bind(ModuleThemeInterface::class, WebtreesTheme::class);

        // Need the routing table, to generate URLs.
        $router_container = new RouterContainer('/');
        (new WebRoutes())->load($router_container->getMap());
        app()->instance(RouterContainer::class, $router_container);

        I18N::init('en-US', true);

        if (static::$uses_database) {
            static::createTestDatabase();

            // Boot modules
            (new ModuleService())->bootModules(new WebtreesTheme());
        }
    }

    /**
     * Things to run once, AFTER all the tests.
     */
    public static function tearDownAfterClass()
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
     * @param string                  $method
     * @param string[]                $query
     * @param string[]                $params
     * @param UploadedFileInterface[] $files
     * @param string[]                $attributes
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

        /** @var ServerRequestInterface $request */
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
    protected function tearDown()
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
        $tree_service = new TreeService();
        $tree         = $tree_service->create(basename($gedcom_file), basename($gedcom_file));
        $stream       = app(StreamFactoryInterface::class)->createStreamFromFile(__DIR__ . '/data/' . $gedcom_file);

        $tree->importGedcomFile($stream, $gedcom_file);

        $timeout_service = new TimeoutService(microtime(true));
        $controller      = new GedcomLoad($timeout_service);
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
}
