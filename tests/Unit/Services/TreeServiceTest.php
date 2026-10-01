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
use Fisharebest\Webtrees\Enums\Role;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TreeService::class)]
class TreeServiceTest extends TestCase
{
    public function testAllRespectsPublicMemberManagerAndAdministratorAccess(): void
    {
        self::createDatabase();

        $service = new TreeService(new GedcomImportService());
        $public_tree = $service->create('public', 'Public');
        $private_tree = $service->create('private', 'Private');
        $unimported_tree = $service->create('unimported', 'Unimported');
        $visitor_tree = $service->create('visitor', 'Visitor');

        foreach (
            [
                [$public_tree->id(), 1, 0],
                [$private_tree->id(), 1, 1],
                [$unimported_tree->id(), 0, 1],
                [$visitor_tree->id(), 1, 1],
            ] as [$tree_id, $imported, $private]
        ) {
            DB::query()
                ->from('gedcom')
                ->whereEqual('gedcom_id', $tree_id)
                ->update(['imported' => $imported, 'private' => $private]);
        }

        Registry::cache()->array()->forget('all-trees');
        self::assertSame(['public'], $service->all()->keys()->all());
        self::assertSame(['public' => 'Public'], $service->titles());

        $user = (new UserService(new SystemClock()))->create('tester', 'Tester', 'tester@example.com', 'secret');
        Auth::login($user);
        $private_tree->setUserPreference($user, UserInterface::PREF_TREE_ROLE, Role::Member->value);
        $unimported_tree->setUserPreference($user, UserInterface::PREF_TREE_ROLE, Role::Visitor->value);
        $visitor_tree->setUserPreference($user, UserInterface::PREF_TREE_ROLE, Role::Visitor->value);

        Registry::cache()->array()->forget('all-trees');
        self::assertSame(['private', 'public'], $service->all()->keys()->all());

        $unimported_tree->setUserPreference($user, UserInterface::PREF_TREE_ROLE, Role::Manager->value);
        Registry::cache()->array()->forget('all-trees');
        self::assertSame(['private', 'public', 'unimported'], $service->all()->keys()->all());

        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Registry::cache()->array()->forget('user-' . $user->id());
        Registry::cache()->array()->forget('all-trees');
        self::assertSame(['private', 'public', 'unimported', 'visitor'], $service->all()->keys()->all());
    }

    public function testClass(): void
    {
        self::assertTrue(class_exists(TreeService::class));
    }

    public function testCreateCopiesDefaultSettingsAndRestrictions(): void
    {
        self::createDatabase();

        DB::query(table: 'gedcom_setting')->insert([
            'gedcom_id'     => -1,
            'setting_name'  => 'test-setting',
            'setting_value' => 'test-value',
        ]);
        DB::query(table: 'default_resn')->insert([
            'gedcom_id' => -1,
            'xref'      => 'I1',
            'tag_type'  => 'NAME',
            'resn'      => 'privacy',
            'comment'   => 'test-comment',
        ]);

        $tree = (new TreeService(new GedcomImportService()))->create('name', 'title');

        self::assertSame('test-value', DB::query()
            ->from('gedcom_setting')
            ->whereEqual('gedcom_id', $tree->id())
            ->whereEqual('setting_name', 'test-setting')
            ->value('setting_value'));
        self::assertSame('privacy', DB::query()
            ->from('default_resn')
            ->whereEqual('gedcom_id', $tree->id())
            ->whereEqual('tag_type', 'NAME')
            ->value('resn'));
    }

    public function testDeleteRemovesOnlyThisTreesBlockSettings(): void
    {
        self::createDatabase();

        $service    = new TreeService(new GedcomImportService());
        $tree       = $service->create('first', 'First');
        $other_tree = $service->create('second', 'Second');
        DB::query(table: 'module')->insert(['module_name' => 'test-block']);

        foreach ([[$tree->id(), 101], [$other_tree->id(), 102]] as [$tree_id, $block_id]) {
            DB::query(table: 'block')->insert([
                'block_id'    => $block_id,
                'gedcom_id'   => $tree_id,
                'block_order' => 1,
                'module_name' => 'test-block',
            ]);
            DB::query(table: 'block_setting')->insert([
                'block_id'      => $block_id,
                'setting_name'  => 'test',
                'setting_value' => 'value',
            ]);
        }

        $service->delete($tree);

        self::assertSame([102], DB::query()->from('block_setting')->pluck('block_id'));
        self::assertSame([102], DB::query()->from('block')->pluck('block_id'));
    }
}
