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
use Fisharebest\Webtrees\Factories\CacheFactory;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(Statistics::class)]
#[CoversClass(StatisticsData::class)]
#[CoversClass(StatisticsData::class)]
#[CoversClass(StatisticsFormat::class)]
class EmbeddedVariablesTest extends TestCase
{
    private const PLACEHOLDERS = [
        '#getAllTagsTable#',
        '#ageBetweenSpousesFM#',
        '#ageBetweenSpousesFM:5#',
        '#ageBetweenSpousesFMList#',
        '#ageBetweenSpousesFMList:5#',
        '#ageBetweenSpousesMF#',
        '#ageBetweenSpousesMF:5#',
        '#ageBetweenSpousesMFList#',
        '#ageBetweenSpousesMFList:5#',
        '#averageChildren#',
        '#averageLifespan#',
        '#averageLifespan:1#',
        '#averageLifespanFemale#',
        '#averageLifespanFemale:1#',
        '#averageLifespanMale#',
        '#averageLifespanMale:1#',
        '#browserDate#',
        '#browserTime#',
        '#browserTimezone#',
        '#callBlock#',
        '#callBlock:gedcom_block#',
        '#callBlock:review_changes:sendmail=0:days=2#',
        '#chartCommonGiven#',
        '#chartCommonGiven:ffffff:000000:5#',
        '#chartDistribution#',
        '#chartDistribution:150#',
        '#chartDistribution:world:surname_distribution_chart#',
        '#chartDistribution:world:surname_distribution_chart:windsor#',
        '#chartDistribution:world:birth_distribution_chart#',
        '#chartDistribution:world:death_distribution_chart#',
        '#chartDistribution:world:marriage_distribution_chart#',
        '#chartDistribution:world:indi_distribution_chart#',
        '#chartFamsWithSources#',
        '#chartIndisWithSources#',
        '#chartLargestFamilies#',
        '#chartLargestFamilies:ffffff:000000:5#',
        '#chartMedia#',
        '#chartMortality#',
        '#chartNoChildrenFamilies#',
        '#chartSex#',
        '#commonBirthPlacesList#',
        '#commonBirthPlacesList:5#',
        '#commonCountriesList#',
        '#commonCountriesList:5#',
        '#commonDeathPlacesList#',
        '#commonDeathPlacesList:5#',
        '#commonGiven#',
        '#commonGiven:5:5#',
        '#commonGivenFemale:5:5#',
        '#commonGivenFemale#',
        '#commonGivenFemale:5:5#',
        '#commonGivenFemaleList#',
        '#commonGivenFemaleList:5:5#',
        '#commonGivenFemaleListTotals#',
        '#commonGivenFemaleListTotals:5:5#',
        '#commonGivenFemaleTable#',
        '#commonGivenFemaleTable:5:5#',
        '#commonGivenFemaleTotals#',
        '#commonGivenFemaleTotals:5:5#',
        '#commonGivenList#',
        '#commonGivenList:5:5#',
        '#commonGivenListTotals#',
        '#commonGivenListTotals:5:5#',
        '#commonGivenMale#',
        '#commonGivenMale:5:5#',
        '#commonGivenMaleList#',
        '#commonGivenMaleList:5:5#',
        '#commonGivenMaleListTotals#',
        '#commonGivenMaleListTotals:5:5#',
        '#commonGivenMaleTable#',
        '#commonGivenMaleTable:5:5#',
        '#commonGivenMaleTotals#',
        '#commonGivenMaleTotals:5:5#',
        '#commonGivenOther#',
        '#commonGivenOther:5:5#',
        '#commonGivenOtherList#',
        '#commonGivenOtherList:5:5#',
        '#commonGivenOtherListTotals#',
        '#commonGivenOtherListTotals:5:5#',
        '#commonGivenOtherTable#',
        '#commonGivenOtherTable:5:5#',
        '#commonGivenOtherTotals#',
        '#commonGivenOtherTotals:5:5#',
        '#commonGivenTable#',
        '#commonGivenTable:5:5#',
        '#commonGivenTotals#',
        '#commonGivenTotals:5:5#',
        '#commonGivenUnknown#',
        '#commonGivenUnknown:5:5#',
        '#commonGivenUnknownList#',
        '#commonGivenUnknownList:5:5#',
        '#commonGivenUnknownListTotals#',
        '#commonGivenUnknownListTotals:5:5#',
        '#commonGivenUnknownTable#',
        '#commonGivenUnknownTable:5:5#',
        '#commonGivenUnknownTotals#',
        '#commonGivenUnknownTotals:5:5#',
        '#commonMarriagePlacesList#',
        '#commonMarriagePlacesList:5:5#',
        '#commonSurnames#',
        '#commonSurnames:5:5:alpha#',
        '#commonSurnames:5:5:count#',
        '#commonSurnames:5:5:rcount#',
        '#commonSurnamesList#',
        '#commonSurnamesList:5:5:alpha#',
        '#commonSurnamesList:5:5:count##',
        '#commonSurnamesList:5:5:rcount#',
        '#commonSurnamesListTotals#',
        '#commonSurnamesListTotals:5:5:alpha#',
        '#commonSurnamesListTotals:5:5:count##',
        '#commonSurnamesListTotals:5:5:rcount#',
        '#commonSurnamesTotals#',
        '#commonSurnamesTotals:5:5:alpha#',
        '#commonSurnamesTotals:5:5:count##',
        '#commonSurnamesTotals:5:5:rcount#',
        '#contactGedcom#',
        '#contactWebmaster#',
        '#firstBirth#',
        '#firstBirthName#',
        '#firstBirthPlace#',
        '#firstBirthYear#',
        '#firstDeath#',
        '#firstDeathName#',
        '#firstDeathPlace#',
        '#firstDeathYear#',
        '#firstDivorce#',
        '#firstDivorceName#',
        '#firstDivorcePlace#',
        '#firstDivorceYear#',
        '#firstEvent#',
        '#firstEventName#',
        '#firstEventPlace#',
        '#firstEventType#',
        '#firstEventYear#',
        '#firstMarriage#',
        '#firstMarriageName#',
        '#firstMarriagePlace#',
        '#firstMarriageYear#',
        '#gedcomCreatedSoftware#',
        '#gedcomCreatedVersion#',
        '#gedcomDate#',
        '#gedcomFavorites#',
        '#gedcomFilename#',
        '#gedcomRootId#',
        '#gedcomTitle#',
        '#gedcomUpdated#',
        '#getCommonSurname#',
        '#hitCount#',
        '#hitCountFam#',
        '#hitCountFam:X1#',
        '#hitCountIndi#',
        '#hitCountIndi:X1#',
        '#hitCountNote#',
        '#hitCountNote:X1#',
        '#hitCountObje#',
        '#hitCountObje:X1#',
        '#hitCountRepo#',
        '#hitCountRepo:X1#',
        '#hitCountSour#',
        '#hitCountSour:X1#',
        '#hitCountUser#',
        '#largestFamily#',
        '#largestFamilyName#',
        '#largestFamilySize#',
        '#lastBirth#',
        '#lastBirthName#',
        '#lastBirthPlace#',
        '#lastBirthYear#',
        '#lastDeath#',
        '#lastDeathName#',
        '#lastDeathPlace#',
        '#lastDeathYear#',
        '#lastDivorce#',
        '#lastDivorceName#',
        '#lastDivorcePlace#',
        '#lastDivorceYear#',
        '#lastEvent#',
        '#lastEventName#',
        '#lastEventPlace#',
        '#lastEventType#',
        '#lastEventYear#',
        '#lastMarriage#',
        '#lastMarriageName#',
        '#lastMarriagePlace#',
        '#lastMarriageYear#',
        '#latestUserFullName#',
        '#latestUserId#',
        '#latestUserLoggedin#',
        '#latestUserLoggedin:Oui:Non#',
        '#latestUserName#',
        '#latestUserRegDate#',
        '#latestUserRegDate:%j %F %Y#',
        '#latestUserRegTime#',
        '#latestUserRegTime:%H:%i:%s#',
        '#longestLife#',
        '#longestLifeAge#',
        '#longestLifeFemale#',
        '#longestLifeFemaleAge#',
        '#longestLifeFemaleName#',
        '#longestLifeMale#',
        '#longestLifeMaleAge#',
        '#longestLifeMaleName#',
        '#longestLifeName#',
        '#minAgeOfMarriage#',
        '#minAgeOfMarriageFamilies#',
        '#minAgeOfMarriageFamilies:5#',
        '#minAgeOfMarriageFamiliesList#',
        '#minAgeOfMarriageFamiliesList:5#',
        '#minAgeOfMarriageFamily#',
        '#noChildrenFamilies#',
        '#noChildrenFamiliesList#',
        '#noChildrenFamiliesList:nolist#',
        '#oldestFather#',
        '#oldestFatherAge#',
        '#oldestFatherName#',
        '#oldestMarriageFemale#',
        '#oldestMarriageFemaleAge#',
        '#oldestMarriageFemaleAge:1#',
        '#oldestMarriageFemaleName#',
        '#oldestMarriageMale#',
        '#oldestMarriageMaleAge#',
        '#oldestMarriageMaleAge:1#',
        '#oldestMarriageMaleName#',
        '#oldestMother#',
        '#oldestMotherAge#',
        '#oldestMotherAge:1#',
        '#oldestMotherName#',
        '#serverDate#',
        '#serverTime#',
        '#serverTime24#',
        '#serverTimezone#',
        '#statsAge#',
        '#statsBirth#',
        '#statsChildren#',
        '#statsDeath#',
        '#statsDiv#',
        '#statsMarr#',
        '#statsMarrAge#',
        '#topAgeBetweenSiblings#',
        '#topAgeBetweenSiblingsFullName#',
        '#topAgeBetweenSiblingsList#',
        '#topAgeBetweenSiblingsList:5:1#',
        '#topAgeBetweenSiblingsName#',
        '#topAgeOfMarriage#',
        '#topAgeOfMarriageFamilies#',
        '#topAgeOfMarriageFamilies:5#',
        '#topAgeOfMarriageFamiliesList#',
        '#topAgeOfMarriageFamiliesList:5#',
        '#topAgeOfMarriageFamily#',
        '#topTenLargestFamily#',
        '#topTenLargestFamily:5#',
        '#topTenLargestFamilyList#',
        '#topTenLargestFamilyList:5#',
        '#topTenLargestGrandFamily#',
        '#topTenLargestGrandFamily:5#',
        '#topTenLargestGrandFamilyList#',
        '#topTenLargestGrandFamilyList:5#',
        '#topTenOldest#',
        '#topTenOldest:5#',
        '#topTenOldestAlive#',
        '#topTenOldestAlive:5#',
        '#topTenOldestFemale#',
        '#topTenOldestFemale:5#',
        '#topTenOldestFemaleAlive#',
        '#topTenOldestFemaleAlive:5#',
        '#topTenOldestFemaleList#',
        '#topTenOldestFemaleList:5#',
        '#topTenOldestFemaleListAlive#',
        '#topTenOldestFemaleListAlive:5#',
        '#topTenOldestList#',
        '#topTenOldestList:5#',
        '#topTenOldestListAlive#',
        '#topTenOldestListAlive:5#',
        '#topTenOldestMale#',
        '#topTenOldestMale:5#',
        '#topTenOldestMaleAlive#',
        '#topTenOldestMaleAlive:5#',
        '#topTenOldestMaleList#',
        '#topTenOldestMaleList:5#',
        '#topTenOldestMaleListAlive#',
        '#topTenOldestMaleListAlive:5#',
        '#totalAdmins#',
        '#totalBirths#',
        '#totalChildren#',
        '#totalDeaths#',
        '#totalDeceased#',
        '#totalDeceasedPercentage#',
        '#totalDivorces#',
        '#totalEvents#',
        '#totalEventsBirth#',
        '#totalEventsDeath#',
        '#totalEventsDivorce#',
        '#totalEventsMarriage#',
        '#totalEventsOther#',
        '#totalFamilies#',
        '#totalFamiliesPercentage#',
        '#totalFamsWithSources#',
        '#totalFamsWithSourcesPercentage#',
        '#totalGedcomFavorites#',
        '#totalGivennames#',
        '#totalGivennames:Charles#',
        '#totalIndisWithSources#',
        '#totalIndisWithSourcesPercentage#',
        '#totalIndividuals#',
        '#totalIndividualsPercentage#',
        '#totalLiving#',
        '#totalLivingPercentage#',
        '#totalMarriages#',
        '#totalMarriedFemales#',
        '#totalMarriedMales#',
        '#totalMedia#',
        '#totalMediaAudio#',
        '#totalMediaBook#',
        '#totalMediaCard#',
        '#totalMediaCertificate#',
        '#totalMediaCoatOfArms#',
        '#totalMediaDocument#',
        '#totalMediaElectronic#',
        '#totalMediaFiche#',
        '#totalMediaFilm#',
        '#totalMediaMagazine#',
        '#totalMediaManuscript#',
        '#totalMediaMap#',
        '#totalMediaNewspaper#',
        '#totalMediaOther#',
        '#totalMediaPainting#',
        '#totalMediaPhoto#',
        '#totalMediaTombstone#',
        '#totalMediaUnknown#',
        '#totalMediaVideo#',
        '#totalNonAdmins#',
        '#totalNotes#',
        '#totalNotesPercentage#',
        '#totalPlaces#',
        '#totalRecords#',
        '#totalRepositories#',
        '#totalRepositoriesPercentage#',
        '#totalSexFemales#',
        '#totalSexFemalesPercentage#',
        '#totalSexMales#',
        '#totalSexMalesPercentage#',
        '#totalSexOther#',
        '#totalSexOtherPercentage#',
        '#totalSexUnknown#',
        '#totalSexUnknownPercentage#',
        '#totalSources#',
        '#totalSourcesPercentage#',
        '#totalSurnames#',
        '#totalSurnames:Spencer#',
        '#totalTreeNews#',
        '#totalUserFavorites#',
        '#totalUserJournal#',
        '#totalUserMessages#',
        '#totalUsers#',
        '#userFavorites#',
        '#userFullName#',
        '#userId#',
        '#userName#',
        '#userName:Foo Bar#',
        '#usersLoggedIn#',
        '#usersLoggedInList#',
        '#webtreesVersion#',
        '#youngestFather#',
        '#youngestFatherAge#',
        '#youngestFatherAge:1#',
        '#youngestFatherName#',
        '#youngestMarriageFemale#',
        '#youngestMarriageFemaleAge#',
        '#youngestMarriageFemaleAge:1#',
        '#youngestMarriageFemaleName#',
        '#youngestMarriageMale#',
        '#youngestMarriageMaleAge#',
        '#youngestMarriageMaleAge:1#',
        '#youngestMarriageMaleName#',
        '#youngestMother#',
        '#youngestMotherAge#',
        '#youngestMotherAge:1#',
        '#youngestMotherName#',
    ];

    protected static bool $uses_database = true;

    public function testAllEmbeddedVariables(): void
    {
        $user_service = new UserService();
        $tree    = $this->importTree('demo.ged');
        $request = self::createRequest()->withAttribute('tree', $tree);
        Registry::container()->set(ServerRequestInterface::class, $request);

        $statistics = new Statistics(
            new ModuleService(),
            $tree,
            $user_service
        );

        // As visitor
        Registry::cache(new CacheFactory());
        foreach (self::PLACEHOLDERS as $placeholder) {
            $text = $statistics->embedTags($placeholder);
            self::assertNotEquals($placeholder, $text);
        }

        // As member
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);
        Registry::cache(new CacheFactory());
        foreach (self::PLACEHOLDERS as $placeholder) {
            $text = $statistics->embedTags($placeholder);
            self::assertNotEquals($placeholder, $text);
        }
    }

    public function testAllEmbeddedVariablesWithEmptyTree(): void
    {
        $user_service          = new UserService();
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $request               = self::createRequest()->withAttribute('tree', $tree);
        Registry::container()->set(ServerRequestInterface::class, $request);

        $statistics = new Statistics(
            new ModuleService(),
            $tree,
            $user_service
        );

        // As visitor
        Registry::cache(new CacheFactory());
        foreach (self::PLACEHOLDERS as $placeholder) {
            $text = $statistics->embedTags($placeholder);
            self::assertNotEquals($placeholder, $text);
        }

        // As member
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);
        Registry::cache(new CacheFactory());

        foreach (self::PLACEHOLDERS as $placeholder) {
            $text = $statistics->embedTags($placeholder);
            self::assertNotEquals($placeholder, $text);
        }
    }
}
