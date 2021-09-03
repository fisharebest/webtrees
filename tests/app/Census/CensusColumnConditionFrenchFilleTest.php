<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;

/**
 * Test harness for the class CensusColumnConditionFrenchFille
 */
class CensusColumnConditionFrenchFilleTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFille
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testNoSpouseFamiliesMale(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('M');
        $individual->method('spouseFamilies')->willReturn(new Collection());
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1800'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1830');

        $column = new CensusColumnConditionFrenchFille($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFille
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testNoSpouseFamiliesFemale(): void
    {
        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('spouseFamilies')->willReturn(new Collection());
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1800'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1830');

        $column = new CensusColumnConditionFrenchFille($census, '', '');

        self::assertSame('1', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFille
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testNoFamilyFactsMale(): void
    {
        $family = $this->createMock(Family::class);
        $family->method('getMarriageDate')->willReturn(new Date(''));
        $family->method('facts')->with(['MARR'])->willReturn(new Collection());

        $individual = $this->createMock(Individual::class);
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1800'));
        $individual->method('sex')->willReturn('M');

        $census = $this->createMock(CensusInterface::class);

        $column = new CensusColumnConditionFrenchFille($census, '', '');
        $census->method('censusDate')->willReturn('30 JUN 1830');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFille
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testNoFamilyFactsFemale(): void
    {
        $family = $this->createMock(Family::class);
        $family->method('getMarriageDate')->willReturn(new Date(''));
        $family->method('facts')->with(['MARR'])->willReturn(new Collection());

        $individual = $this->createMock(Individual::class);
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1800'));
        $individual->method('sex')->willReturn('F');

        $census = $this->createMock(CensusInterface::class);

        $column = new CensusColumnConditionFrenchFille($census, '', '');
        $census->method('censusDate')->willReturn('30 JUN 1830');

        self::assertSame('1', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFille
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testSpouseDeadMale(): void
    {
        $fact = $this->createMock(Fact::class);

        $spouse = $this->createMock(Individual::class);
        $spouse->method('getDeathDate')->willReturn(new Date('1820'));

        $family = $this->createMock(Family::class);
        $family
            ->expects(self::exactly(2))
            ->method('facts')
            ->withConsecutive(
                [['MARR']],
                [['DIV']]
            )
            ->willReturnOnConsecutiveCalls(
                new Collection([$fact]),
                new Collection()
            );
        $family->expects(self::once())->method('spouse')->willReturn($spouse);

        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('M');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = $this->createMock(CensusInterface::class);

        $column = new CensusColumnConditionFrenchFille($census, '', '');
        $census->method('censusDate')->willReturn('30 JUN 1830');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFille
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testSpouseDeadFemale(): void
    {
        $fact = $this->createMock(Fact::class);

        $spouse = $this->createMock(Individual::class);
        $spouse->method('getDeathDate')->willReturn(new Date('1820'));

        $family = $this->createMock(Family::class);
        $family
            ->expects(self::exactly(2))
            ->method('facts')
            ->withConsecutive(
                [['MARR']],
                [['DIV']]
            )
            ->willReturnOnConsecutiveCalls(
                new Collection([$fact]),
                new Collection()
            );
        $family->expects(self::once())->method('spouse')->willReturn($spouse);

        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = $this->createMock(CensusInterface::class);

        $column = new CensusColumnConditionFrenchFille($census, '', '');
        $census->method('censusDate')->willReturn('30 JUN 1830');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFille
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testNoFamilyUnmarriedMale(): void
    {
        $family = $this->createMock(Family::class);
        $family->method('getMarriageDate')->willReturn(new Date(''));
        $family->method('facts')->with(['MARR'])->willReturn(new Collection());

        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('M');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1800'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1830');

        $column = new CensusColumnConditionFrenchFille($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFille
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testNoFamilyUnmarriedFemale(): void
    {
        $family = $this->createMock(Family::class);
        $family->method('getMarriageDate')->willReturn(new Date(''));
        $family->method('facts')->with(['MARR'])->willReturn(new Collection());

        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1800'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1830');

        $column = new CensusColumnConditionFrenchFille($census, '', '');

        self::assertSame('1', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFille
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testChildMale(): void
    {
        $family = $this->createMock(Family::class);
        $family->method('getMarriageDate')->willReturn(new Date(''));
        $family->method('facts')->with(['MARR'])->willReturn(new Collection());

        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('M');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1820'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1830');

        $column = new CensusColumnConditionFrenchFille($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFille
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testChildFemale(): void
    {
        $family = $this->createMock(Family::class);
        $family->method('getMarriageDate')->willReturn(new Date(''));
        $family->method('facts')->with(['MARR'])->willReturn(new Collection());

        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1820'));

        $census = $this->createMock(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1830');

        $column = new CensusColumnConditionFrenchFille($census, '', '');

        self::assertSame('1', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFille
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testDivorcedMale(): void
    {
        $fact = $this->createMock(Fact::class);

        $family = $this->createMock(Family::class);
        $family
            ->expects(self::exactly(2))
            ->method('facts')
            ->withConsecutive(
                [['MARR']],
                [['DIV']]
            )
            ->willReturn(
                new Collection([$fact]),
                new Collection([$fact])
            );

        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('M');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = $this->createMock(CensusInterface::class);

        $column = new CensusColumnConditionFrenchFille($census, '', '');
        $census->method('censusDate')->willReturn('30 JUN 1830');

        self::assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchFille
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testDivorcedFemale(): void
    {
        $fact = $this->createMock(Fact::class);

        $family = $this->createMock(Family::class);
        $family
            ->expects(self::exactly(2))
            ->method('facts')
            ->withConsecutive(
                [['MARR']],
                [['DIV']]
            )
            ->willReturn(
                new Collection([$fact]),
                new Collection([$fact])
            );

        $individual = $this->createMock(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = $this->createMock(CensusInterface::class);

        $column = new CensusColumnConditionFrenchFille($census, '', '');
        $census->method('censusDate')->willReturn('30 JUN 1830');

        self::assertSame('', $column->generate($individual, $individual));
    }
}
