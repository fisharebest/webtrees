<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CensusColumnRelationToHead::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusColumnRelationToHeadTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testNull(): void
    {
        $individual1 = $this->createMock(Individual::class);
        $individual2 = $this->createMock(Individual::class);

        $individual1->method('childFamilies')->willReturn(new Collection());
        $individual1->method('spouseFamilies')->willReturn(new Collection());
        $individual2->method('childFamilies')->willReturn(new Collection());
        $individual2->method('spouseFamilies')->willReturn(new Collection());

        $census = $this->createMock(CensusInterface::class);

        $column = new CensusColumnRelationToHead($census, '', '');

        self::assertSame('-', $column->generate($individual1, $individual1));
        self::assertSame('', $column->generate($individual1, $individual2));
    }
}
