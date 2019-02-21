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
 * \Fisharebest\Webtrees\Statistics\Repository\BrowserRepository
 * \Fisharebest\Webtrees\Statistics\Repository\ServerRepository
 * \Fisharebest\Webtrees\Statistics\Repository\LatestUserRepository
 * \Fisharebest\Webtrees\Statistics\Repository\FamilyDatesRepository
 * \Fisharebest\Webtrees\Statistics\Repository\HitCountRepository
 * \Fisharebest\Webtrees\Statistics\Repository\NewsRepository
 * \Fisharebest\Webtrees\Statistics\Repository\FavoritesRepository
 * \Fisharebest\Webtrees\Statistics\Repository\IndividualRepository
 * \Fisharebest\Webtrees\Statistics\Repository\MediaRepository
 * \Fisharebest\Webtrees\Statistics\Repository\MessageRepository
 * \Fisharebest\Webtrees\Statistics\Repository\ContactRepository
 * \Fisharebest\Webtrees\Statistics\Repository\GedcomRepository
 * \Fisharebest\Webtrees\Statistics\Repository\FamilyRepository
 * \Fisharebest\Webtrees\Statistics\Repository\EventRepository
 * \Fisharebest\Webtrees\Statistics\Repository\PlaceRepository
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\MediaRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\MessageRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\HitCountRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\FavoritesRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\GedcomRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\EventRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\UserRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\PlaceRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\IndividualRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\NewsRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\FamilyDatesRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\ContactRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\ServerRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\BrowserRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\Interfaces\LatestUserRepositoryInterface
 * \Fisharebest\Webtrees\Statistics\Repository\UserRepository
 * \Fisharebest\Webtrees\Statistics\AbstractGoogle
 * \Fisharebest\Webtrees\Statistics\Google\ChartChildren
 * \Fisharebest\Webtrees\Statistics\Google\ChartAge
 * \Fisharebest\Webtrees\Statistics\Google\ChartCommonGiven
 * \Fisharebest\Webtrees\Statistics\Google\ChartMarriageAge
 * \Fisharebest\Webtrees\Statistics\Google\ChartCommonSurname
 * \Fisharebest\Webtrees\Statistics\Google\ChartDistribution
 * \Fisharebest\Webtrees\Statistics\Google\ChartFamilyLargest
 * \Fisharebest\Webtrees\Statistics\Google\ChartNoChildrenFamilies
 * \Fisharebest\Webtrees\Statistics\Google\ChartSex
 * \Fisharebest\Webtrees\Statistics\Google\ChartMedia
 * \Fisharebest\Webtrees\Statistics\Google\ChartMarriage
 * \Fisharebest\Webtrees\Statistics\Google\ChartFamilyWithSources
 * \Fisharebest\Webtrees\Statistics\Google\ChartMortality
 * \Fisharebest\Webtrees\Statistics\Google\ChartDeath
 * \Fisharebest\Webtrees\Statistics\Google\ChartIndividualWithSources
 * \Fisharebest\Webtrees\Statistics\Google\ChartBirth
 * \Fisharebest\Webtrees\Statistics\Google\ChartDivorce
 * \Fisharebest\Webtrees\Statistics\Helper\Country
 * \Fisharebest\Webtrees\Statistics\Helper\Century
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

        $tree       = $this->importTree('demo.ged');
        $statistics = new Statistics(new ModuleService(), $tree, new UserService());

        $text = $statistics->embedTags('#getAllTagsTable#');

        $this->assertNotEquals('#getAllTagsTable#', $text);
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

        $text = $statistics->embedTags('#getAllTagsTable#');

        $this->assertNotEquals('#getAllTagsTable#', $text);
    }
}
