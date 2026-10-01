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
use Fisharebest\Webtrees\Module\FrequentlyAskedQuestionsModule;
use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\HtmlService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FrequentlyAskedQuestionsModule::class)]
class FrequentlyAskedQuestionsModuleTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(FrequentlyAskedQuestionsModule::class));
    }

    public function testListsTreeFaqsAndMovesBlocks(): void
    {
        self::createDatabase();
        (new ModuleService())->bootModules(new WebtreesTheme());

        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');
        $module       = new FrequentlyAskedQuestionsModule(new HtmlService(), $tree_service);
        $module->setName('faq');

        $global_id = DB::query(table: 'block')->insertGetId([
            'module_name' => 'faq', 'gedcom_id' => null, 'block_order' => 1,
        ], 'block_id');
        $local_id = DB::query(table: 'block')->insertGetId([
            'module_name' => 'faq', 'gedcom_id' => $tree->id(), 'block_order' => 3,
        ], 'block_id');
        $other_id = DB::query(table: 'block')->insertGetId([
            'module_name' => 'faq', 'gedcom_id' => $other_tree->id(), 'block_order' => 7,
        ], 'block_id');

        $faqs = [
            [$global_id, 'Global question', 'fr'],
            [$local_id, 'Local question', 'en-US'],
            [$other_id, 'Other question', 'en-US'],
        ];
        foreach ($faqs as [$block_id, $header, $languages]) {
            DB::query(table: 'block_setting')->insertBatch([
                ['block_id' => $block_id, 'setting_name' => 'header', 'setting_value' => $header],
                ['block_id' => $block_id, 'setting_name' => 'faqbody', 'setting_value' => 'Answer'],
                ['block_id' => $block_id, 'setting_name' => 'languages', 'setting_value' => $languages],
            ]);
        }

        self::assertNotNull($module->getMenu($tree));
        $admin_request = self::createRequest(attributes: ['tree' => $tree]);
        $admin_html    = (string) $module->getAdminAction($admin_request)->getBody();
        self::assertMatchesRegularExpression('/Global question.*Local question/s', $admin_html);
        self::assertStringNotContainsString('Other question', $admin_html);

        $show_html = (string) $module->getShowAction($admin_request)->getBody();
        self::assertStringContainsString('Local question', $show_html);
        self::assertStringNotContainsString('Global question', $show_html);

        $new_html = (string) $module->getAdminEditAction(self::createRequest())->getBody();
        self::assertStringContainsString('id="block_order" class="form-control" value="8"', $new_html);

        $edit_request = self::createRequest(query: ['block_id' => (string) $local_id]);
        $edit_html    = (string) $module->getAdminEditAction($edit_request)->getBody();
        self::assertStringContainsString('Local question', $edit_html);
        self::assertStringContainsString('id="block_order" class="form-control" value="3"', $edit_html);

        $move_request = self::createRequest(query: ['block_id' => (string) $global_id]);
        $module->postAdminMoveDownAction($move_request);
        self::assertSame(3, (int) DB::query()
            ->from('block')
            ->whereEqual('block_id', $global_id)
            ->value('block_order'));
        self::assertSame(1, (int) DB::query()
            ->from('block')
            ->whereEqual('block_id', $local_id)
            ->value('block_order'));

        $module->postAdminMoveUpAction($move_request);
        self::assertSame(1, (int) DB::query()
            ->from('block')
            ->whereEqual('block_id', $global_id)
            ->value('block_order'));
        self::assertSame(3, (int) DB::query()
            ->from('block')
            ->whereEqual('block_id', $local_id)
            ->value('block_order'));
    }
}
