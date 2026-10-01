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
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Module\FamilyTreeFavoritesModule;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FamilyTreeFavoritesModule::class)]
class FamilyTreeFavoritesModuleTest extends TestCase
{
    public function testClassExists(): void
    {
        self::assertTrue(class_exists(FamilyTreeFavoritesModule::class));
    }

    public function testDeleteFavoriteOnlyRemovesTreeFavorite(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree = (new TreeService(new GedcomImportService()))->create('test', 'Test');

        $tree_favorite_id = DB::query(table: 'favorite')->insertGetId([
            'gedcom_id'     => $tree->id(),
            'user_id'       => null,
            'favorite_type' => 'URL',
            'url'           => 'https://example.com/',
        ], 'favorite_id');

        $user_favorite_id = DB::query(table: 'favorite')->insertGetId([
            'gedcom_id'     => $tree->id(),
            'user_id'       => $user->id(),
            'favorite_type' => 'URL',
            'url'           => 'https://example.com/',
        ], 'favorite_id');

        $request = self::createRequest(
            query: ['favorite_id' => (string) $tree_favorite_id],
            attributes: ['tree' => $tree],
        )->withAttribute('user', $user);

        (new FamilyTreeFavoritesModule())->postDeleteFavoriteAction($request);

        self::assertSame(0, DB::query(table: 'favorite')->whereEqual('favorite_id', $tree_favorite_id)->count());
        self::assertSame(1, DB::query(table: 'favorite')->whereEqual('favorite_id', $user_favorite_id)->count());
    }

    public function testGetsOnlyTreeFavoritesWithMappedRecords(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');

        DB::query(table: 'favorite')->insertBatch([
            [
                'gedcom_id'     => $tree->id(),
                'user_id'       => null,
                'xref'          => null,
                'favorite_type' => 'URL',
                'url'           => 'https://example.com/',
                'title'         => 'Example',
                'note'          => 'Link note',
            ],
            [
                'gedcom_id'     => $tree->id(),
                'user_id'       => null,
                'xref'          => 'X1',
                'favorite_type' => 'INDI',
                'url'           => null,
                'title'         => null,
                'note'          => 'Record note',
            ],
            [
                'gedcom_id'     => $tree->id(),
                'user_id'       => $user->id(),
                'xref'          => null,
                'favorite_type' => 'URL',
                'url'           => 'https://private.example.com/',
                'title'         => null,
                'note'          => null,
            ],
            [
                'gedcom_id'     => $other_tree->id(),
                'user_id'       => null,
                'xref'          => null,
                'favorite_type' => 'URL',
                'url'           => 'https://other.example.com/',
                'title'         => null,
                'note'          => null,
            ],
        ]);

        $favorites = (new FamilyTreeFavoritesModule())->getFavorites($tree);
        self::assertCount(2, $favorites);

        $by_type = [];
        foreach ($favorites as $favorite) {
            $by_type[$favorite->favorite_type] = $favorite;
        }

        self::assertSame('Example', $by_type['URL']->title);
        self::assertSame('https://example.com/', $by_type['URL']->url);
        self::assertNull($by_type['URL']->record);
        self::assertSame('Record note', $by_type['INDI']->note);
        self::assertInstanceOf(GedcomRecord::class, $by_type['INDI']->record);
        self::assertSame('X1', $by_type['INDI']->record->xref());
    }

    public function testRepeatedFavoriteSubmissionsCreateSeparateRows(): void
    {
        self::createDatabase();

        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);
        $tree = (new TreeService(new GedcomImportService()))->create('test', 'Test');
        $module = new FamilyTreeFavoritesModule();

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
                ->whereNull('user_id')
                ->whereEqual('favorite_type', $favorite_type)
                ->orderBy('favorite_id')
                ->pluck('note'));
        }
    }
}
