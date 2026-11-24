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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Contracts\GedcomRecordFactoryInterface;
use Fisharebest\Webtrees\Contracts\SourceFactoryInterface;
use Fisharebest\Webtrees\Elements\DescriptiveTitle;
use Fisharebest\Webtrees\Elements\XrefSource;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(View::class)]
class FactGedcomFieldsTest extends TestCase
{
    public function testUnknownFieldsRespectTreePreference(): void
    {
        Registry::elementFactory()->registerTags([
            'INDI:TITL'      => new DescriptiveTitle('Title'),
            'INDI:SOUR'      => new XrefSource('Source'),
            'INDI:SOUR:PAGE' => new DescriptiveTitle('Page'),
        ]);
        Registry::gedcomRecordFactory(self::createStub(GedcomRecordFactoryInterface::class));

        $source_factory = self::createStub(SourceFactoryInterface::class);
        $source_factory->method('make')->willReturn(self::createStub(Source::class));
        Registry::sourceFactory($source_factory);

        foreach (
            [
                "1 TITL known-value\n1 _BAD unknown-value",
                "1 SOUR @S1@\n2 PAGE known-value\n2 _BAD unknown-value",
            ] as $gedcom
        ) {
            $tree = self::createStub(Tree::class);
            $tree->method('getPreference')->willReturn('1');
            $html = view('fact-gedcom-fields', ['gedcom' => $gedcom, 'parent' => 'INDI', 'tree' => $tree]);

            self::assertStringContainsString('known-value', $html);
            self::assertStringNotContainsString('unknown-value', $html);

            $tree = self::createStub(Tree::class);
            $tree->method('getPreference')->willReturn('0');
            $html = view('fact-gedcom-fields', ['gedcom' => $gedcom, 'parent' => 'INDI', 'tree' => $tree]);

            self::assertStringContainsString('known-value', $html);
            self::assertStringContainsString('unknown-value', $html);
        }
    }
}
