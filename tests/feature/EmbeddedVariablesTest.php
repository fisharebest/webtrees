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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Statistics\Google\ChartAge;
use Fisharebest\Webtrees\Statistics\Google\ChartBirth;
use Fisharebest\Webtrees\Statistics\Google\ChartChildren;
use Fisharebest\Webtrees\Statistics\Google\ChartCommonGiven;
use Fisharebest\Webtrees\Statistics\Google\ChartCommonSurname;
use Fisharebest\Webtrees\Statistics\Google\ChartDeath;
use Fisharebest\Webtrees\Statistics\Google\ChartDistribution;
use Fisharebest\Webtrees\Statistics\Google\ChartDivorce;
use Fisharebest\Webtrees\Statistics\Google\ChartFamilyLargest;
use Fisharebest\Webtrees\Statistics\Google\ChartFamilyWithSources;
use Fisharebest\Webtrees\Statistics\Google\ChartIndividualWithSources;
use Fisharebest\Webtrees\Statistics\Google\ChartMarriage;
use Fisharebest\Webtrees\Statistics\Google\ChartMarriageAge;
use Fisharebest\Webtrees\Statistics\Google\ChartMedia;
use Fisharebest\Webtrees\Statistics\Google\ChartMortality;
use Fisharebest\Webtrees\Statistics\Google\ChartNoChildrenFamilies;
use Fisharebest\Webtrees\Statistics\Google\ChartSex;
use Fisharebest\Webtrees\Statistics\Repository\BrowserRepository;
use Fisharebest\Webtrees\Statistics\Repository\ContactRepository;
use Fisharebest\Webtrees\Statistics\Repository\EventRepository;
use Fisharebest\Webtrees\Statistics\Repository\FamilyDatesRepository;
use Fisharebest\Webtrees\Statistics\Repository\FamilyRepository;
use Fisharebest\Webtrees\Statistics\Repository\FavoritesRepository;
use Fisharebest\Webtrees\Statistics\Repository\GedcomRepository;
use Fisharebest\Webtrees\Statistics\Repository\HitCountRepository;
use Fisharebest\Webtrees\Statistics\Repository\IndividualRepository;
use Fisharebest\Webtrees\Statistics\Repository\LatestUserRepository;
use Fisharebest\Webtrees\Statistics\Repository\MediaRepository;
use Fisharebest\Webtrees\Statistics\Repository\MessageRepository;
use Fisharebest\Webtrees\Statistics\Repository\NewsRepository;
use Fisharebest\Webtrees\Statistics\Repository\PlaceRepository;
use Fisharebest\Webtrees\Statistics\Repository\ServerRepository;
use Fisharebest\Webtrees\Statistics\Repository\UserRepository;
use Fisharebest\Webtrees\Statistics\Service\CenturyService;
use Fisharebest\Webtrees\Statistics\Service\ColorService;
use Fisharebest\Webtrees\Statistics\Service\CountryService;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(Statistics::class)]
#[CoversClass(BrowserRepository::class)]
#[CoversClass(ServerRepository::class)]
#[CoversClass(LatestUserRepository::class)]
#[CoversClass(FamilyDatesRepository::class)]
#[CoversClass(HitCountRepository::class)]
#[CoversClass(NewsRepository::class)]
#[CoversClass(FavoritesRepository::class)]
#[CoversClass(IndividualRepository::class)]
#[CoversClass(MediaRepository::class)]
#[CoversClass(MessageRepository::class)]
#[CoversClass(ContactRepository::class)]
#[CoversClass(GedcomRepository::class)]
#[CoversClass(FamilyRepository::class)]
#[CoversClass(EventRepository::class)]
#[CoversClass(PlaceRepository::class)]
#[CoversClass(UserRepository::class)]
#[CoversClass(ChartChildren::class)]
#[CoversClass(ChartAge::class)]
#[CoversClass(ChartCommonGiven::class)]
#[CoversClass(ChartMarriageAge::class)]
#[CoversClass(ChartCommonSurname::class)]
#[CoversClass(ChartDistribution::class)]
#[CoversClass(ChartFamilyLargest::class)]
#[CoversClass(ChartNoChildrenFamilies::class)]
#[CoversClass(ChartSex::class)]
#[CoversClass(ChartMedia::class)]
#[CoversClass(ChartMarriage::class)]
#[CoversClass(ChartFamilyWithSources::class)]
#[CoversClass(ChartMortality::class)]
#[CoversClass(ChartDeath::class)]
#[CoversClass(ChartIndividualWithSources::class)]
#[CoversClass(ChartBirth::class)]
#[CoversClass(ChartDivorce::class)]
#[CoversClass(CountryService::class)]
#[CoversClass(CenturyService::class)]
class EmbeddedVariablesTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testAllEmbeddedVariables(): void
    {
        $user_service = new UserService();

        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree       = $this->importTree('demo.ged');
        $request    = self::createRequest()->withAttribute('tree', $tree);
        Registry::container()->set(ServerRequestInterface::class, $request);

        $statistics = new Statistics(
            new CenturyService(),
            new ColorService(),
            new CountryService(),
            new ModuleService(),
            $tree,
            $user_service
        );

        // As member
        $text = $statistics->embedTags('#getAllTagsTable#');
        self::assertNotEquals('#getAllTagsTable#', $text);

        // As visitor
        $text = $statistics->embedTags('#getAllTagsTable#');
        self::assertNotEquals('#getAllTagsTable#', $text);
    }

    public function testAllEmbeddedVariablesWithEmptyTree(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $statistics            = new Statistics(
            new CenturyService(),
            new ColorService(),
            new CountryService(),
            new ModuleService(),
            $tree,
            new UserService()
        );

        // As visitor
        $text = $statistics->embedTags('#getAllTagsTable#');
        self::assertNotEquals('#getAllTagsTable#', $text);

        // As member
        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $text = $statistics->embedTags('#getAllTagsTable#');
        self::assertNotEquals('#getAllTagsTable#', $text);
    }
}
