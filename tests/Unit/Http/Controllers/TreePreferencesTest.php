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
use Fisharebest\Webtrees\Http\Controllers\TreePreferences;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function array_fill_keys;

#[CoversClass(TreePreferences::class)]
class TreePreferencesTest extends TestCase
{
    public function testRejectsDuplicateTreeNameButAllowsUniqueName(): void
    {
        self::createDatabase();

        $tree_service = new TreeService(new GedcomImportService());
        $user_service = new UserService(new SystemClock());
        $tree         = $tree_service->create('first', 'First');
        $tree_service->create('second', 'Second');
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $parameters = array_fill_keys([
            'EXPAND_NOTES', 'EXPAND_SOURCES', 'GENERATE_UIDS', 'HIDE_GEDCOM_ERRORS',
            'NO_UPDATE_CHAN', 'SHOW_COUNTER', 'SHOW_EST_LIST_DATES', 'SHOW_FACT_ICONS',
            'SHOW_GEDCOM_RECORD', 'SHOW_HIGHLIGHT_IMAGES', 'SHOW_LAST_CHANGE',
            'SHOW_PARENTS_AGE', 'USE_SILHOUETTE',
        ], '0') + array_fill_keys([
            'MEDIA_UPLOAD', 'SHOW_MEDIA_DOWNLOAD', 'SHOW_NO_WATERMARK', 'SHOW_PEDIGREE_PLACES',
            'SHOW_PEDIGREE_PLACES_SUFFIX', 'SUBLIST_TRIGGER_I',
        ], '0') + [
            'CALENDAR_FORMAT0'    => 'gregorian',
            'CALENDAR_FORMAT1'    => 'gregorian',
            'FORMAT_TEXT'         => '',
            'MEDIA_DIRECTORY'     => 'media/',
            'META_DESCRIPTION'    => '',
            'META_TITLE'          => '',
            'PEDIGREE_ROOT_ID'    => '',
            'SURNAME_LIST_STYLE'  => 'style1',
            'SURNAME_TRADITION'   => 'paternal',
            'title'               => 'Updated',
        ];

        $controller = new TreePreferences($tree_service, $user_service);
        $request    = self::createRequest(HttpRequestMethod::POST->value, params: $parameters + ['gedcom' => 'second'], attributes: ['tree' => $tree]);
        $controller->post($request, $tree);

        self::assertSame('first', DB::query(table: 'gedcom')->whereEqual('gedcom_id', $tree->id())->value('gedcom_name'));

        $request = self::createRequest(HttpRequestMethod::POST->value, params: $parameters + ['gedcom' => 'renamed'], attributes: ['tree' => $tree]);
        $controller->post($request, $tree);

        self::assertSame('renamed', DB::query(table: 'gedcom')->whereEqual('gedcom_id', $tree->id())->value('gedcom_name'));
    }
}
