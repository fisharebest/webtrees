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

namespace Fisharebest\Webtrees\Tests\Unit\Services;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GedcomImportService::class)]
class GedcomImportServiceTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(GedcomImportService::class));
    }

    public function testUpdateRecordRemovesOrphanedPlacesButKeepsLinkedPlaces(): void
    {
        self::createDatabase();

        $service = new GedcomImportService();
        $tree    = (new TreeService($service))->create('first', 'First');

        $orphan_id = DB::query(table: 'places')->insertGetId([
            'p_place' => 'Orphan place',
            'p_file'  => $tree->id(),
        ], 'p_id');
        $linked_id = DB::query(table: 'places')->insertGetId([
            'p_place' => 'Linked place',
            'p_file'  => $tree->id(),
        ], 'p_id');

        DB::query(table: 'placelinks')->insert(['pl_p_id' => $orphan_id, 'pl_gid' => 'I1', 'pl_file' => $tree->id()]);
        DB::query(table: 'placelinks')->insert(['pl_p_id' => $linked_id, 'pl_gid' => 'I2', 'pl_file' => $tree->id()]);

        $service->updateRecord('0 @I1@ INDI', $tree, true);

        self::assertSame([$linked_id], DB::query()->from('places')->whereIn('p_id', [$orphan_id, $linked_id])->pluck('p_id'));
    }

    public function testRetainsLinkedMediaWhenImportingARecordWithoutMediaLinks(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $import_service = new GedcomImportService();
        $tree           = (new TreeService($import_service))->create('first', 'First');
        $import_service->importRecord("0 @M1@ OBJE\n1 FILE image.jpg\n2 FORM jpg", $tree, true);
        $import_service->importRecord("0 @I1@ INDI\n1 OBJE @M1@", $tree, true);

        $tree->setPreference('keep_media', '1');
        DB::query()
            ->from('individuals')
            ->whereEqual('i_file', $tree->id())
            ->whereEqual('i_id', 'I1')
            ->delete();
        DB::query()
            ->from('name')
            ->whereEqual('n_file', $tree->id())
            ->whereEqual('n_id', 'I1')
            ->delete();
        $import_service->importRecord("0 @I1@ INDI\n1 SEX M", $tree, true);

        self::assertSame(['M1'], DB::query()
            ->from('link')
            ->whereEqual('l_file', $tree->id())
            ->whereEqual('l_from', 'I1')
            ->whereEqual('l_type', 'OBJE')
            ->pluck('l_to'));
    }

    public function testReusesInlineMediaWithTheSameTitleAndFilenameInOneTree(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $import_service = new GedcomImportService();
        $tree_service   = new TreeService($import_service);
        $tree           = $tree_service->create('first', 'First');
        $other_tree     = $tree_service->create('second', 'Second');
        $inline_media   = "\n1 OBJE\n2 FILE shared.jpg\n2 FORM jpg\n2 TITL Shared";

        $import_service->importRecord("0 @I1@ INDI\n1 NAME First /Person/" . $inline_media, $tree, true);
        $import_service->importRecord("0 @I2@ INDI\n1 NAME Second /Person/" . $inline_media, $tree, true);
        $import_service->importRecord("0 @I3@ INDI\n1 NAME Other /Person/" . $inline_media, $other_tree, true);

        $media_ids = DB::query()
            ->from('media_file')
            ->whereEqual('m_file', $tree->id())
            ->pluck('m_id');

        self::assertCount(1, $media_ids);
        self::assertSame([$media_ids[0], $media_ids[0]], DB::query()
            ->from('link')
            ->whereEqual('l_file', $tree->id())
            ->whereIn('l_from', ['I1', 'I2'])
            ->whereEqual('l_type', 'OBJE')
            ->orderBy('l_from')
            ->pluck('l_to'));
        self::assertSame(1, DB::query()
            ->from('media_file')
            ->whereEqual('m_file', $other_tree->id())
            ->count());
    }
}
