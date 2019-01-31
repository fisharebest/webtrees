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

use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;

/**
 * Test the user functions
 *
 * @coversNothing
 */
class EmbeddedVariablesTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testAllEmbeddedVariables(): void
    {
        global $tree; // For Date::display()

        $tree = $this->importTree('demo.ged');
        $statistics = new Statistics(new ModuleService(), $tree, new UserService());

        //$text = $statistics->embedTags('#getAllTagsTable#');

        //$this->assertNotEquals('#getAllTagsTable#', $text);
    }

    /**
     * @return void
     */
    public function testAllEmbeddedVariablesWithEmptyTree(): void
    {
        global $tree; // For Date::display()

        $tree = Tree::create('name', 'title');
        $tree->deleteGenealogyData(false);
        $statistics = new Statistics(new ModuleService(), $tree, new UserService());

        //$text = $statistics->embedTags('#getAllTagsTable#');

        //$this->assertNotEquals('#getAllTagsTable#', $text);
    }
}
