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
use Fisharebest\Webtrees\Module\ReviewChangesModule;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ReviewChangesModule::class)]
class ReviewChangesModuleTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(ReviewChangesModule::class));
    }

    public function testListsOnlyTheLatestPendingChangePerRecord(): void
    {
        self::createDatabase();

        $clock        = new SystemClock();
        $user_service = new UserService($clock);
        $user         = $user_service->create('admin', 'Admin', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');

        $rows = [
            [$tree->id(), 'I1', 'Old pending', 'pending'],
            [$tree->id(), 'I1', 'Latest pending', 'pending'],
            [$tree->id(), 'I2', 'Accepted', 'accepted'],
            [$other_tree->id(), 'I3', 'Other tree', 'pending'],
        ];

        foreach ($rows as [$tree_id, $xref, $name, $status]) {
            DB::query(table: 'change')->insert([
                'gedcom_id'  => $tree_id,
                'xref'       => $xref,
                'status'     => $status,
                'old_gedcom' => '',
                'new_gedcom' => "0 @" . $xref . "@ INDI\n1 NAME " . $name,
                'user_id'    => $user->id(),
            ]);
        }

        $module = new ReviewChangesModule(new EmailService(), $tree_service, $user_service, $clock);
        $html   = $module->getBlock($tree, 0, ModuleBlockInterface::CONTEXT_EMBED, ['sendmail' => '0']);

        self::assertStringContainsString('Latest pending', $html);
        self::assertStringNotContainsString('Old pending', $html);
        self::assertStringNotContainsString('Accepted', $html);
        self::assertStringNotContainsString('Other tree', $html);
    }
}
