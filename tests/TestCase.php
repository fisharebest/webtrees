<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use function basename;
use Fisharebest\Webtrees\Schema\SeedDatabase;
use Illuminate\Database\Capsule\Manager as DB;
use function file_get_contents;

/**
 * Base class for unit tests
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        parent::setUp();

        defined('WT_BASE_URL') || define('WT_BASE_URL', 'http://localhost/');
        defined('WT_DATA_DIR') || define('WT_DATA_DIR', 'data/');
        defined('WT_ROOT') || define('WT_ROOT', '');
        I18N::init('en-US');
    }

    /**
     * Create an SQLite in-memory database for testing
     */
    protected function createTestDatabase(): void
    {
        $capsule = new DB();
        $capsule->addConnection([
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);
        $capsule->setAsGlobal();

        // Create tables
        Database::updateSchema('\Fisharebest\Webtrees\Schema', 'WT_SCHEMA_VERSION', Webtrees::SCHEMA_VERSION);

        // Create config data
        (new SeedDatabase())->run();
    }

    /**
     * Import a GEDCOM file into the test database.
     *
     * @param string $gedcom_file
     */
    protected function importTree(string $gedcom_file): void
    {
        $x = DB::table('gedcom')->insert([
            'gedcom_name' => basename($gedcom_file),
        ]);

        var_dump($x);exit;

        DB::table('gedcom_chunk')->insert([
            'chunk_data' => file_get_contents($gedcom_file),
        ]);
    }
}
