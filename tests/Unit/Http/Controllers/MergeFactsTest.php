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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\HttpRequestMethod;
use Fisharebest\Webtrees\Http\Controllers\MergeFacts;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function array_map;

#[CoversClass(MergeFacts::class)]
class MergeFactsTest extends TestCase
{
    public function testMergesHitCountersByPageName(): void
    {
        self::createDatabase();

        $import_service = new GedcomImportService();
        $tree           = (new TreeService($import_service))->create('name', 'title');
        $user           = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $import_service->importRecord('0 @N1@ NOTE First', $tree, true);
        $import_service->importRecord('0 @N2@ NOTE Second', $tree, true);

        DB::query(table: 'hit_counter')->insertBatch([
            ['gedcom_id' => $tree->id(), 'page_name' => 'note', 'page_parameter' => 'N1', 'page_count' => 3],
            ['gedcom_id' => $tree->id(), 'page_name' => 'note', 'page_parameter' => 'N2', 'page_count' => 4],
            ['gedcom_id' => $tree->id(), 'page_name' => 'record', 'page_parameter' => 'N1', 'page_count' => 0],
            ['gedcom_id' => $tree->id(), 'page_name' => 'record', 'page_parameter' => 'N2', 'page_count' => 5],
            ['gedcom_id' => $tree->id(), 'page_name' => 'note', 'page_parameter' => 'X1', 'page_count' => 9],
        ]);

        $request = self::createRequest(HttpRequestMethod::POST->value, params: [
            'xref1' => 'N1',
            'xref2' => 'N2',
            'keep1' => [],
            'keep2' => [],
        ], attributes: ['tree' => $tree]);

        (new MergeFacts($user, new LinkedRecordService()))->post($request, $tree);

        $rows = DB::query()
            ->select('page_name', 'page_parameter', 'page_count')
            ->from('hit_counter')
            ->whereEqual('gedcom_id', $tree->id())
            ->orderBy('page_name', 'page_parameter')
            ->get();
        $hits = array_map(static fn (object $row): array => [$row->page_name, $row->page_parameter, $row->page_count], $rows);

        self::assertSame([
            ['note', 'N1', 7],
            ['note', 'X1', 9],
            ['record', 'N1', 5],
        ], $hits);
    }
}
