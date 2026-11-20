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

use DateTimeImmutable;
use DateTimeZone;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\HttpRequestMethod;
use Fisharebest\Webtrees\Http\Controllers\TreePrivacy;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;

use function date;

#[CoversClass(TreePrivacy::class)]
class TreePrivacyTest extends TestCase
{
    public function testListsOnlyPrivacyRulesForTheCurrentTree(): void
    {
        self::createDatabase();

        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('first', 'First');
        $other_tree   = $tree_service->create('second', 'Second');
        self::createRequest(attributes: ['tree' => $tree]);

        DB::query(table: 'default_resn')->whereIn('gedcom_id', [$tree->id(), $other_tree->id()])->delete();
        $rule_id = DB::query(table: 'default_resn')->insertGetId([
            'gedcom_id' => $tree->id(),
            'xref'      => 'Z7',
            'tag_type'  => null,
            'resn'      => 'privacy',
            'updated'   => date('Y-m-d H:i:s'),
        ], 'default_resn_id');
        $other_rule_id = DB::query(table: 'default_resn')->insertGetId([
            'gedcom_id' => $other_tree->id(),
            'xref'      => 'Z8',
            'tag_type'  => null,
            'resn'      => 'privacy',
            'updated'   => date('Y-m-d H:i:s'),
        ], 'default_resn_id');

        $html = (string) (new TreePrivacy($tree_service))->get($tree)->getBody();

        self::assertStringContainsString('Z7', $html);
        self::assertStringContainsString('id="delete-' . $rule_id . '"', $html);
        self::assertStringNotContainsString('Z8', $html);
        self::assertStringNotContainsString('id="delete-' . $other_rule_id . '"', $html);
    }

    public function testPostSetsCurrentTimestampOnPrivacyRules(): void
    {
        self::createDatabase();

        $tree_service = new TreeService(new GedcomImportService());
        $created_tree = $tree_service->create('first', 'First');
        $tree = self::createStub(Tree::class);
        $tree->method('id')->willReturn($created_tree->id());
        $tree->method('name')->willReturn($created_tree->name());
        $tree->method('title')->willReturn($created_tree->title());
        $tree->method('setPreference')->willReturnSelf();

        $request = self::createRequest(HttpRequestMethod::POST->value, params: [
            'delete'                     => [],
            'xref'                       => ['I1'],
            'tag_type'                   => ['NAME'],
            'resn'                       => ['privacy'],
            'HIDE_LIVE_PEOPLE'           => '0',
            'MAX_ALIVE_AGE'              => '100',
            'REQUIRE_AUTHENTICATION'     => '0',
            'SHOW_DEAD_PEOPLE'           => '0',
            'SHOW_LIVING_NAMES'          => '0',
            'SHOW_PRIVATE_RELATIONSHIPS' => '0',
        ], attributes: ['tree' => $tree]);

        $before = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s.u');
        (new TreePrivacy($tree_service))->post($request, $tree);
        $after = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s.u');

        $updated = DB::query()
            ->from('default_resn')
            ->whereEqual('gedcom_id', $tree->id())
            ->whereEqual('xref', 'I1')
            ->whereEqual('tag_type', 'NAME')
            ->value('updated');
        self::assertGreaterThanOrEqual($before, $updated);
        self::assertLessThanOrEqual($after, $updated);
    }
}
