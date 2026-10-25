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

namespace Fisharebest\Webtrees\Tests\Unit\Census;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Fisharebest\Webtrees\Census\AbstractCensusColumn;
use Fisharebest\Webtrees\Census\CensusColumnBirthYear;
use Fisharebest\Webtrees\Census\CensusInterface;

#[CoversClass(CensusColumnBirthYear::class)]
#[CoversClass(AbstractCensusColumn::class)]
class CensusColumnBirthYearTest extends TestCase
{
    public function testGenerateColumn(): void
    {
        $individual = self::createStub(Individual::class);
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('02 JAN 1800'));

        $census = self::createStub(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1832');

        $column = new CensusColumnBirthYear($census, '', '');

        I18N::init('en-GB');

        self::assertSame('1800', $column->generate($individual, $individual));
    }
}
