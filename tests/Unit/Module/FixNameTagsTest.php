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

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Module\FixNameTags;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FixNameTags::class)]
class FixNameTagsTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(FixNameTags::class));
    }

    public function testIndividualsToFixMatchesAnyNameTagWithinRange(): void
    {
        self::createDatabase();

        $tree = (new TreeService(new GedcomImportService()))->create('name', 'title');
        $fix  = new FixNameTags(new DataFixService());

        $individuals = [
            'I1' => "0 @I1@ INDI\n1 NAME First\n2 _AKA Other",
            'I2' => "0 @I2@ INDI\n1 NAME Second\n2 _MARNM Married",
            'I3' => "0 @I3@ INDI\n1 NAME Unchanged",
        ];

        foreach ($individuals as $xref => $gedcom) {
            DB::query(table: 'individuals')->insert([
                'i_id' => $xref, 'i_file' => $tree->id(), 'i_rin' => '', 'i_sex' => 'U', 'i_gedcom' => $gedcom,
            ]);
        }

        self::assertEqualsCanonicalizing(['I1', 'I2'], $fix->individualsToFix($tree, [])->all());
        self::assertSame(['I2'], $fix->individualsToFix($tree, ['start' => 'I2', 'end' => 'I3'])->all());
    }
}
