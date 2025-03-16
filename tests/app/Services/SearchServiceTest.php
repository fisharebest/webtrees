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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SearchService::class)]
class SearchServiceTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testSearchesReturnCollections(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $search_service = new SearchService($tree_service);
        $tree = $this->importTree('demo.ged');

        $user = (new UserService())->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $result = $search_service->searchFamilies([$tree], ['windsor'])->all();
        self::assertNotEmpty($result);

        $result = $search_service->searchFamilyNames([$tree], ['charles', 'diana'])->all();
        //self::assertNotEmpty($result);

        $result = $search_service->searchIndividuals([$tree], ['windsor'])->all();
        self::assertNotEmpty($result);

        $result = $search_service->searchIndividualNames([$tree], ['windsor'])->all();
        //self::assertNotEmpty($result);

        $result = $search_service->searchMedia([$tree], ['windsor'])->all();
        self::assertNotEmpty($result);

        $result = $search_service->searchNotes([$tree], ['windsor'])->all();
        //self::assertNotEmpty($result);

        $result = $search_service->searchRepositories([$tree], ['national'])->all();
        self::assertNotEmpty($result);

        $result = $search_service->searchSources([$tree], ['england'])->all();
        self::assertNotEmpty($result);

        $result = $search_service->searchSourcesByName([$tree], ['england'])->all();
        self::assertNotEmpty($result);

        $result = $search_service->searchSubmitters([$tree], ['greg'])->all();
        self::assertNotEmpty($result);

        $result = $search_service->searchPlaces($tree, 'England')->all();
        //self::assertNotEmpty($result);
    }
}
