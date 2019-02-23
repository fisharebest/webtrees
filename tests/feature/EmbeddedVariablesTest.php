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
 * @covers \Fisharebest\Webtrees\Statistics
 * @covers \Fisharebest\Webtrees\Statistics\Repository\BrowserRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\ServerRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\LatestUserRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\FamilyDatesRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\HitCountRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\NewsRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\FavoritesRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\IndividualRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\MediaRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\MessageRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\ContactRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\GedcomRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\FamilyRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\EventRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\PlaceRepository
 * @covers \Fisharebest\Webtrees\Statistics\Repository\UserRepository
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartChildren
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartAge
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartCommonGiven
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartMarriageAge
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartCommonSurname
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartDistribution
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartFamilyLargest
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartNoChildrenFamilies
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartSex
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartMedia
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartMarriage
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartFamilyWithSources
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartMortality
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartDeath
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartIndividualWithSources
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartBirth
 * @covers \Fisharebest\Webtrees\Statistics\Google\ChartDivorce
 * @covers \Fisharebest\Webtrees\Statistics\Service\CountryService
 * @covers \Fisharebest\Webtrees\Statistics\Service\CenturyService
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

        // As visitor
        $text = $statistics->embedTags('#getAllTagsTable#');
        $this->assertNotEquals('#getAllTagsTable#', $text);

        // As member
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference('canadmin', '1');
        Auth::login($user);

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

        // As visitor
        $text = $statistics->embedTags('#getAllTagsTable#');
        $this->assertNotEquals('#getAllTagsTable#', $text);

        // As member
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference('canadmin', '1');
        Auth::login($user);

        $text = $statistics->embedTags('#getAllTagsTable#');
        $this->assertNotEquals('#getAllTagsTable#', $text);
    }
}
