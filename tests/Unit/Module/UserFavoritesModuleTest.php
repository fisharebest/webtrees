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
use Fisharebest\Webtrees\Module\UserFavoritesModule;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserFavoritesModule::class)]
class UserFavoritesModuleTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(UserFavoritesModule::class));
    }

    public function testGetsOnlyFavoritesForTheGivenUserAndTree(): void
    {
        self::createDatabase();

        $user_service = new UserService(new SystemClock());
        $user         = $user_service->create('first', 'First', 'first@example.com', 'secret');
        $other_user   = $user_service->create('second', 'Second', 'second@example.com', 'secret');
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');

        $rows = [
            [$tree->id(), $user->id(), 'Selected'],
            [$tree->id(), $other_user->id(), 'Other user'],
            [$other_tree->id(), $user->id(), 'Other tree'],
        ];

        foreach ($rows as [$tree_id, $user_id, $title]) {
            DB::query(table: 'favorite')->insert([
                'gedcom_id'     => $tree_id,
                'user_id'       => $user_id,
                'favorite_type' => 'URL',
                'url'           => 'https://example.com/',
                'title'         => $title,
            ]);
        }

        $favorites = (new UserFavoritesModule())->getFavorites($tree, $user);

        self::assertCount(1, $favorites);
        self::assertSame('Selected', $favorites[0]->title);
        self::assertNull($favorites[0]->record);
    }

    public function testRepeatedFavoriteSubmissionsCreateSeparateRows(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        Auth::login($user);
        $tree = (new TreeService(new GedcomImportService()))->create('test', 'Test');
        $module = new UserFavoritesModule();

        foreach (['url' => 'URL', 'indi' => 'INDI'] as $type => $favorite_type) {
            foreach (['first', 'second'] as $note) {
                $request = self::createRequest(
                    params: [
                        'note'      => $note,
                        'title'     => 'Link',
                        'url'       => 'https://example.com/',
                        'type'      => $type,
                        'indi-xref' => 'X1',
                    ],
                    attributes: ['tree' => $tree],
                )->withAttribute('user', $user);

                $module->postAddFavoriteAction($request);
            }

            self::assertSame(['first', 'second'], DB::query()
                ->from('favorite')
                ->whereEqual('gedcom_id', $tree->id())
                ->whereEqual('user_id', $user->id())
                ->whereEqual('favorite_type', $favorite_type)
                ->orderBy('favorite_id')
                ->pluck('note'));
        }
    }
}
