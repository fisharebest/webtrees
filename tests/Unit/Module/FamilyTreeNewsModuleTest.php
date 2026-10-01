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
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Module\FamilyTreeNewsModule;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\HtmlService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FamilyTreeNewsModule::class)]
class FamilyTreeNewsModuleTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(FamilyTreeNewsModule::class));
    }

    public function testListsTreeNewsNewestFirstAndEditsOnlyItsOwnArticles(): void
    {
        self::createDatabase();
        (new ModuleService())->bootModules(new WebtreesTheme());

        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');
        $old_news_id  = DB::query(table: 'news')->insertGetId([
            'gedcom_id' => $tree->id(),
            'subject'   => 'Old item',
            'body'      => 'Old content',
            'updated'   => '2020-01-01 12:00:00',
        ], 'news_id');
        DB::query(table: 'news')->insert([
            'gedcom_id' => $tree->id(),
            'subject'   => 'New item',
            'body'      => 'New content',
            'updated'   => '2021-01-01 12:00:00',
        ]);
        $other_news_id = DB::query(table: 'news')->insertGetId([
            'gedcom_id' => $other_tree->id(),
            'subject'   => 'Other tree item',
            'body'      => 'Other content',
            'updated'   => '2022-01-01 12:00:00',
        ], 'news_id');

        $module = new FamilyTreeNewsModule(new HtmlService());
        $html   = $module->getBlock($tree, 1, ModuleBlockInterface::CONTEXT_EMBED);

        self::assertMatchesRegularExpression('/New item.*Old item/s', $html);
        self::assertStringNotContainsString('Other tree item', $html);

        $request = self::createRequest(query: ['news_id' => (string) $old_news_id], attributes: ['tree' => $tree]);
        $edit    = (string) $module->getEditNewsAction($request)->getBody();
        self::assertStringContainsString('Old item', $edit);
        self::assertStringContainsString('Old content', $edit);

        $request = self::createRequest(query: ['news_id' => (string) $other_news_id], attributes: ['tree' => $tree]);
        $this->expectException(HttpNotFoundException::class);
        $module->getEditNewsAction($request);
    }
}
