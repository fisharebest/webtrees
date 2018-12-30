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

/**
 * Test the database migration process
 */
class MigrationTest extends \Fisharebest\Webtrees\TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->createTestDatabase();
    }

    /**
     * Test that the class exists
     *
     * @return void
     */
    public function testTablesExists()
    {
        $tree = $this->importTree(__DIR__ . '/../data/demo.ged');

        $this->assertSame(1, $tree->id());
        $this->assertSame('demo', $tree->name());
        $this->assertSame('demo', $tree->title());
    }
}
