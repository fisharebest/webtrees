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
use Fisharebest\Webtrees\Http\Controllers\SitemapDataXml;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function substr_count;

#[CoversClass(SitemapDataXml::class)]
class SitemapDataXmlTest extends TestCase
{
    public function testListsEachPublicRecordTypeAndPaginates(): void
    {
        self::createDatabase();

        $import_service = new GedcomImportService();
        $tree           = (new TreeService($import_service))->create('name', 'title');
        $tree->setPreference('include_in_sitemap', '1');
        $tree->setPreference('HIDE_LIVE_PEOPLE', '0');

        $import_service->importRecord("0 @I2@ INDI\n1 NAME Jane /DOE/\n1 BIRT\n2 DATE 01 JAN 1850", $tree, true);
        $import_service->importRecord("0 @F1@ FAM\n1 HUSB @X1@", $tree, true);
        $import_service->importRecord("0 @M1@ OBJE\n1 FILE image.jpg", $tree, true);
        $import_service->importRecord('0 @N1@ NOTE Text', $tree, true);
        $import_service->importRecord("0 @R1@ REPO\n1 NAME Repository", $tree, true);
        $import_service->importRecord("0 @S1@ SOUR\n1 TITL Source", $tree, true);
        $import_service->importRecord("0 @U1@ SUBM\n1 NAME Submitter", $tree, true);

        $controller = new SitemapDataXml();

        $expected_xrefs = [
            Family::RECORD_TYPE     => 'F1',
            Individual::RECORD_TYPE => 'I2',
            Media::RECORD_TYPE      => 'M1',
            Note::RECORD_TYPE       => 'N1',
            Repository::RECORD_TYPE => 'R1',
            Source::RECORD_TYPE     => 'S1',
            Submitter::RECORD_TYPE  => 'U1',
        ];

        foreach ($expected_xrefs as $type => $xref) {
            $xml = (string) $controller->get($tree, $type, 0)->getBody();

            self::assertStringContainsString($xref, $xml);
            self::assertSame($type === Individual::RECORD_TYPE ? 3 : 2, substr_count($xml, '<url>'));
        }

        $next_page = (string) $controller->get($tree, Family::RECORD_TYPE, 1)->getBody();
        self::assertSame(1, substr_count($next_page, '<url>'));
    }
}
