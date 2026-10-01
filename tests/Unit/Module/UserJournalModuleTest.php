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
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\UserJournalModule;
use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\HtmlService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserJournalModule::class)]
class UserJournalModuleTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(UserJournalModule::class));
    }

    public function testListsOnlyTheCurrentUsersArticlesNewestFirst(): void
    {
        self::createDatabase();
        (new ModuleService())->bootModules(new WebtreesTheme());

        $user_service = new UserService(new SystemClock());
        $user         = $user_service->create('first', 'First', 'first@example.com', 'secret');
        $other_user   = $user_service->create('second', 'Second', 'second@example.com', 'secret');
        Auth::login($user);
        $tree = (new TreeService(new GedcomImportService()))->create('first', 'First');

        $rows = [
            [$user->id(), 'Old article', '2020-01-01 12:00:00'],
            [$user->id(), 'New article', '2021-01-01 12:00:00'],
            [$other_user->id(), 'Other article', '2022-01-01 12:00:00'],
        ];

        foreach ($rows as [$user_id, $subject, $updated]) {
            DB::query(table: 'news')->insert([
                'user_id' => $user_id,
                'subject' => $subject,
                'body'    => 'Content',
                'updated' => $updated,
            ]);
        }

        $module = new UserJournalModule(new HtmlService());
        $html   = $module->getBlock($tree, 0, ModuleBlockInterface::CONTEXT_EMBED);

        self::assertMatchesRegularExpression('/New article.*Old article/s', $html);
        self::assertStringNotContainsString('Other article', $html);
    }
}
