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

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Factories\CacheFactory;
use Fisharebest\Webtrees\Http\Controllers\SitemapDataXml;
use Fisharebest\Webtrees\Http\Controllers\SitemapIndexXml;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function e;
use function route;
use function substr_count;

#[CoversClass(SitemapIndexXml::class)]
class SitemapIndexXmlTest extends TestCase
{
    public function testIndexIncludesCountsForEnabledTreesOnly(): void
    {
        self::createDatabase();

        $import_service = new GedcomImportService();
        $tree_service   = new TreeService($import_service);
        $controller     = new SitemapIndexXml($tree_service);

        self::assertSame(0, substr_count((string) $controller->get()->getBody(), '<sitemap>'));

        $tree = $tree_service->create('first', 'First');
        $tree->setPreference('include_in_sitemap', '1');
        $other_tree = $tree_service->create('second', 'Second');
        $other_tree->setPreference('include_in_sitemap', '1');
        $disabled_tree = $tree_service->create('third', 'Third');
        DB::query(table: 'gedcom')->whereIn('gedcom_id', [$tree->id(), $other_tree->id(), $disabled_tree->id()])
            ->update(['private' => 0, 'imported' => 1]);

        $import_service->importRecord("0 @F1@ FAM\n1 HUSB @X1@", $tree, true);
        $import_service->importRecord("0 @M1@ OBJE\n1 FILE image.jpg", $tree, true);
        $import_service->importRecord('0 @N1@ NOTE Text', $tree, true);
        $import_service->importRecord("0 @R1@ REPO\n1 NAME Repository", $tree, true);
        $import_service->importRecord("0 @S1@ SOUR\n1 TITL Source", $tree, true);
        $import_service->importRecord("0 @U1@ SUBM\n1 NAME Submitter", $tree, true);
        $import_service->importRecord("0 @N2@ NOTE Other", $other_tree, true);
        $import_service->importRecord("0 @N3@ NOTE Hidden", $disabled_tree, true);

        Registry::cache(new CacheFactory());
        $xml = (string) $controller->get()->getBody();

        self::assertSame(9, substr_count($xml, '<sitemap>'));
        $record_types = [
            Family::RECORD_TYPE,
            Individual::RECORD_TYPE,
            Media::RECORD_TYPE,
            Note::RECORD_TYPE,
            Repository::RECORD_TYPE,
            Source::RECORD_TYPE,
            Submitter::RECORD_TYPE,
        ];

        foreach ($record_types as $type) {
            self::assertStringContainsString(e(route(SitemapDataXml::class, [
                'tree' => $tree->name(),
                'type' => $type,
                'page' => 0,
            ])), $xml);
        }

        self::assertStringContainsString(e(route(SitemapDataXml::class, [
            'tree' => $other_tree->name(),
            'type' => Note::RECORD_TYPE,
            'page' => 0,
        ])), $xml);
        self::assertStringNotContainsString('third', $xml);
    }
}
