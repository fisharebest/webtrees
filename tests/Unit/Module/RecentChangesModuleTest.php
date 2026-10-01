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

namespace Fisharebest\Webtrees\Tests\Unit\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\RecentChangesModule;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RecentChangesModule::class)]
class RecentChangesModuleTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(RecentChangesModule::class));
    }

    public function testShowsLatestAcceptedChangePerRecord(): void
    {
        self::createDatabase();

        $user_service = new UserService(new SystemClock());
        $user         = $user_service->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');
        (new GedcomImportService())->importRecord("0 @I1@ INDI\n1 NAME Latest", $tree, true);

        foreach (
            [
                [$tree->id(), 'I1', 'Older', 'accepted'],
                [$tree->id(), 'I1', 'Latest', 'accepted'],
                [$tree->id(), 'I2', 'Pending', 'pending'],
                [$other_tree->id(), 'I3', 'Other tree', 'accepted'],
            ] as [$tree_id, $xref, $name, $status]
        ) {
            DB::query(table: 'change')->insert([
                'gedcom_id'  => $tree_id,
                'xref'       => $xref,
                'status'     => $status,
                'old_gedcom' => '',
                'new_gedcom' => "0 @" . $xref . "@ INDI\n1 NAME " . $name,
                'user_id'    => $user->id(),
            ]);
        }

        self::assertSame(2, DB::query()->from('change')
            ->whereEqual('gedcom_id', $tree->id())
            ->whereEqual('status', 'accepted')
            ->whereGreaterThan('change_time', Registry::timestampFactory()->now()->subDays(7)->toDateTimeString())
            ->count());

        $module = new RecentChangesModule($user_service);
        $html   = $module->getBlock($tree, 0, ModuleBlockInterface::CONTEXT_EMBED, ['source' => 'database']);

        self::assertStringContainsString('Latest', $html);
        self::assertStringNotContainsString('Older', $html);
        self::assertStringNotContainsString('Pending', $html);
        self::assertStringNotContainsString('Other tree', $html);
    }
}
