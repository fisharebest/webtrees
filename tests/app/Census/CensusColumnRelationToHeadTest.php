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

/**
 * Test harness for the class CensusColumnRelationToHead
 */
class CensusColumnRelationToHeadTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnRelationToHead
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumn
     */
    public function testNull(): void
    {
        $individual = $this->createMock(Individual::class);

        $census = $this->createMock(CensusInterface::class);

        $column = new CensusColumnRelationToHead($census, '', '');

        self::assertSame('-', $column->generate($individual, $individual));
    }
}
