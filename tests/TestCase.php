<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\Controllers\GedcomFileController;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Schema\SeedDatabase;
use Fisharebest\Webtrees\Services\MigrationService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\UserService;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Database\Capsule\Manager as DB;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use Symfony\Component\HttpFoundation\Request;
use function basename;

/**
 * Base class for unit tests
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    protected static $uses_database = false;

    /**
     * Things to run once, before all the tests.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        if (static::$uses_database) {
            defined('WT_ROOT') || define('WT_ROOT', dirname(__DIR__) . '/');

            static::createTestDatabase();
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
     * Things to run before every test.
     */
    protected function setUp()
    {
        parent::setUp();

        // Use an array cache for database calls, etc.
        app()->instance('cache.array', new Repository(new ArrayStore()));

        app()->bind(Tree::class, function () {
            return null;
        });

        app()->instance(UserService::class, new UserService());
        app()->instance(UserInterface::class, new GuestUser());

        app()->instance(Request::class, Request::createFromGlobals());
        app()->instance(Filesystem::class, new Filesystem(new MemoryAdapter()));

        app()->bind(ModuleThemeInterface::class, WebtreesTheme::class);

        defined('WT_ROOT') || define('WT_ROOT', dirname(__DIR__) . '/');
        defined('WT_BASE_URL') || define('WT_BASE_URL', 'http://localhost/');
        defined('WT_DATA_DIR') || define('WT_DATA_DIR', WT_ROOT . 'data/');
        defined('WT_LOCALE') || define('WT_LOCALE', I18N::init('en-US'));

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

        app('cache.array')->flush();

        Site::$preferences                  = [];
        Tree::$trees                        = [];
        GedcomRecord::$gedcom_record_cache  = null;
        GedcomRecord::$pending_record_cache = null;

        Auth::logout();
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
        Database::registerMacros();

        // Create tables
        $migration_service = new MigrationService;
        $migration_service->updateSchema('\Fisharebest\Webtrees\Schema', 'WT_SCHEMA_VERSION', Webtrees::SCHEMA_VERSION);

        // Create config data
        $migration_service->seedDatabase();
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
        $tree = Tree::create(basename($gedcom_file), basename($gedcom_file));
        $tree->importGedcomFile(__DIR__ . '/data/' . $gedcom_file, $gedcom_file);

        View::share('tree', $tree);
        $gedcom_file_controller = new GedcomFileController();

        do {
            $gedcom_file_controller->import(new TimeoutService(microtime(true)), $tree);

            $imported = $tree->getPreference('imported');
        } while (!$imported);

        return $tree;
    }
}
