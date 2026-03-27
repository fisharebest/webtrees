<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SearchService::class)]
class SearchServiceTest extends TestCase
{
    protected static bool $uses_database = true;

    private SearchService $search_service;

    protected function setUp(): void
    {
        parent::setUp();

        $tree_service = new TreeService(new GedcomImportService());
        $this->search_service = new SearchService($tree_service);
    }

    public function testSearchesReturnCollections(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        $result = $this->search_service->searchFamilies([$tree], ['windsor'])->all();
        self::assertNotEmpty($result);

        $result = $this->search_service->searchFamilyNames([$tree], ['charles', 'diana'])->all();
        //self::assertNotEmpty($result);

        $result = $this->search_service->searchIndividuals([$tree], ['windsor'])->all();
        self::assertNotEmpty($result);

        $result = $this->search_service->searchIndividualNames([$tree], ['windsor'])->all();
        //self::assertNotEmpty($result);

        $result = $this->search_service->searchMedia([$tree], ['windsor'])->all();
        self::assertNotEmpty($result);

        $result = $this->search_service->searchNotes([$tree], ['windsor'])->all();
        //self::assertNotEmpty($result);

        $result = $this->search_service->searchRepositories([$tree], ['national'])->all();
        self::assertNotEmpty($result);

        $result = $this->search_service->searchSources([$tree], ['england'])->all();
        self::assertNotEmpty($result);

        $result = $this->search_service->searchSourcesByName([$tree], ['england'])->all();
        self::assertNotEmpty($result);

        $result = $this->search_service->searchSubmitters([$tree], ['greg'])->all();
        self::assertNotEmpty($result);

        $result = $this->search_service->searchPlaces($tree, 'England')->all();
        //self::assertNotEmpty($result);
    }

    /**
     * S01 — Individual search returns specific known persons.
     */
    public function testSearchIndividualsFindsKnownPerson(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        $result = $this->search_service->searchIndividuals([$tree], ['Elizabeth'])->all();

        self::assertNotEmpty($result, 'Should find individuals matching "Elizabeth"');
        self::assertGreaterThanOrEqual(1, count($result));
    }

    /**
     * S02 — Family search returns families with matching names.
     */
    public function testSearchFamiliesFindsMatchingFamilies(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        $result = $this->search_service->searchFamilies([$tree], ['windsor'])->all();

        self::assertNotEmpty($result, 'Should find families matching "windsor"');
    }

    /**
     * S04 — Search with non-matching term returns empty collection.
     */
    public function testSearchWithNonMatchingTermReturnsEmpty(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        $result = $this->search_service->searchIndividuals([$tree], ['xyznonexistent'])->all();

        self::assertEmpty($result, 'Non-matching search should return empty');
    }

    /**
     * S04 — Multi-word search narrows results.
     */
    public function testSearchWithMultipleTermsNarrowsResults(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        $broad = $this->search_service->searchIndividuals([$tree], ['Elizabeth'])->all();
        $narrow = $this->search_service->searchIndividuals([$tree], ['Elizabeth', 'Windsor'])->all();

        self::assertGreaterThanOrEqual(
            count($narrow),
            count($broad),
            'Multi-word search should return same or fewer results than single-word'
        );
    }

    /**
     * S03 — Source search returns matching sources.
     */
    public function testSearchSourcesFindsMatchingSources(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        $result = $this->search_service->searchSources([$tree], ['england'])->all();

        self::assertNotEmpty($result, 'Should find sources matching "england"');
    }

    /**
     * S03 — Repository search returns matching repositories.
     */
    public function testSearchRepositoriesFindsMatchingRepos(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        $result = $this->search_service->searchRepositories([$tree], ['national'])->all();

        self::assertNotEmpty($result, 'Should find repositories matching "national"');
    }

    /**
     * S12 — Guest user (not logged in) gets restricted search results.
     */
    public function testSearchAsGuestReturnsRestrictedResults(): void
    {
        $tree = $this->importTree('demo.ged');

        // Search without login (guest)
        $guest_result = $this->search_service->searchIndividuals([$tree], ['windsor'])->all();

        // Search as admin
        $this->loginAsAdmin();
        $admin_result = $this->search_service->searchIndividuals([$tree], ['windsor'])->all();

        // Guest should see equal or fewer results than admin
        self::assertGreaterThanOrEqual(
            count($guest_result),
            count($admin_result),
            'Admin should see at least as many results as guest'
        );
    }

    /**
     * S07 — Place search returns matching places.
     */
    public function testSearchPlacesFindsMatchingPlaces(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        $result = $this->search_service->searchPlaces($tree, 'England')->all();

        self::assertNotEmpty($result, 'Should find places matching "England"');
    }

    /**
     * S07 — Place search with no match returns empty.
     */
    public function testSearchPlacesReturnsEmptyForNonMatch(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        $result = $this->search_service->searchPlaces($tree, 'xyznonexistent')->all();

        self::assertEmpty($result, 'Non-matching place search should return empty');
    }

    /**
     * S10 — Media search returns matching media objects.
     */
    public function testSearchMediaFindsMatchingMedia(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        $result = $this->search_service->searchMedia([$tree], ['windsor'])->all();

        self::assertNotEmpty($result, 'Should find media matching "windsor"');
    }

    /**
     * S22 — Submitter search returns matching submitters.
     */
    public function testSearchSubmittersFindsMatchingSubmitters(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->loginAsAdmin();

        $result = $this->search_service->searchSubmitters([$tree], ['greg'])->all();

        self::assertNotEmpty($result, 'Should find submitters matching "greg"');
    }

    private function loginAsAdmin(): void
    {
        $user = (new UserService())->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);
    }
}
