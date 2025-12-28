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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CensusColumnConditionCanadaWidowed::class)]
#[CoversClass(AbstractCensusColumnCondition::class)]
class CensusColumnConditionCanadaWidowedTest extends TestCase
{
    public function testNoSpouseFamiliesMale(): void
    {
        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('M');
        $individual->method('spouseFamilies')->willReturn(new Collection());
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1800'));

        $census = self::createStub(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1830');

        $column = new CensusColumnConditionCanadaWidowed($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testNoSpouseFamiliesFemale(): void
    {
        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('spouseFamilies')->willReturn(new Collection());
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1800'));

        $census = self::createStub(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1830');

        $column = new CensusColumnConditionCanadaWidowed($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testNoFamilyFactsMale(): void
    {
        $family = self::createStub(Family::class);
        $family->method('getMarriageDate')->willReturn(new Date(''));
        $family->method('facts')->with(['MARR'])->willReturn(new Collection());

        $individual = self::createStub(Individual::class);
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1800'));
        $individual->method('sex')->willReturn('M');

        $census = self::createStub(CensusInterface::class);

        $column = new CensusColumnConditionCanadaWidowed($census, '', '');
        $census->method('censusDate')->willReturn('30 JUN 1830');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testNoFamilyFactsFemale(): void
    {
        $family = self::createStub(Family::class);
        $family->method('getMarriageDate')->willReturn(new Date(''));
        $family->method('facts')->with(['MARR'])->willReturn(new Collection());

        $individual = self::createStub(Individual::class);
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1800'));
        $individual->method('sex')->willReturn('F');

        $census = self::createStub(CensusInterface::class);

        $column = new CensusColumnConditionCanadaWidowed($census, '', '');
        $census->method('censusDate')->willReturn('30 JUN 1830');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testSpouseDeadMale(): void
    {
        $fact = self::createStub(Fact::class);

        $spouse = self::createStub(Individual::class);
        $spouse->method('getDeathDate')->willReturn(new Date('1820'));

        $family = $this->createMock(Family::class);
        $family->expects($this->once())->method('getMarriageDate')->willReturn(new Date(''));
        $family->expects($this->exactly(2))
            ->method('facts')
            ->with(self::withConsecutive([['MARR'], ['DIV']]))
            ->willReturnOnConsecutiveCalls(
                new Collection([$fact]),
                new Collection()
            );
        $family->expects($this->once())->method('spouse')->willReturn($spouse);

        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('M');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = self::createStub(CensusInterface::class);

        $column = new CensusColumnConditionCanadaWidowed($census, '', '');
        $census->method('censusDate')->willReturn('30 JUN 1830');

        self::assertSame('W', $column->generate($individual, $individual));
    }

    public function testSpouseDeadFemale(): void
    {
        $fact = self::createStub(Fact::class);

        $spouse = self::createStub(Individual::class);
        $spouse->method('getDeathDate')->willReturn(new Date('1820'));

        $family = $this->createMock(Family::class);
        $family->expects($this->once())->method('getMarriageDate')->willReturn(new Date(''));
        $family
            ->expects($this->exactly(2))
            ->method('facts')
            ->with(self::withConsecutive([['MARR'], ['DIV']]))
            ->willReturnOnConsecutiveCalls(
                new Collection([$fact]),
                new Collection()
            );
        $family->expects($this->once())->method('spouse')->willReturn($spouse);

        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = self::createStub(CensusInterface::class);

        $column = new CensusColumnConditionCanadaWidowed($census, '', '');
        $census->method('censusDate')->willReturn('30 JUN 1830');

        self::assertSame('W', $column->generate($individual, $individual));
    }

    public function testNoFamilyUnmarriedMale(): void
    {
        $family = self::createStub(Family::class);
        $family->method('getMarriageDate')->willReturn(new Date(''));
        $family->method('facts')->with(['MARR'])->willReturn(new Collection());

        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('M');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1800'));

        $census = self::createStub(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1830');

        $column = new CensusColumnConditionCanadaWidowed($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testNoFamilyUnmarriedFemale(): void
    {
        $family = self::createStub(Family::class);
        $family->method('getMarriageDate')->willReturn(new Date(''));
        $family->method('facts')->with(['MARR'])->willReturn(new Collection());

        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1800'));

        $census = self::createStub(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1830');

        $column = new CensusColumnConditionCanadaWidowed($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testChildMale(): void
    {
        $family = self::createStub(Family::class);
        $family->method('getMarriageDate')->willReturn(new Date(''));
        $family->method('facts')->with(['MARR'])->willReturn(new Collection());

        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('M');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1820'));

        $census = self::createStub(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1830');

        $column = new CensusColumnConditionCanadaWidowed($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testChildFemale(): void
    {
        $family = self::createStub(Family::class);
        $family->method('getMarriageDate')->willReturn(new Date(''));
        $family->method('facts')->with(['MARR'])->willReturn(new Collection());

        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));
        $individual->method('getEstimatedBirthDate')->willReturn(new Date('1820'));

        $census = self::createStub(CensusInterface::class);
        $census->method('censusDate')->willReturn('30 JUN 1830');

        $column = new CensusColumnConditionCanadaWidowed($census, '', '');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testDivorcedMale(): void
    {
        $fact = self::createStub(Fact::class);

        $family = $this->createMock(Family::class);
        $family->expects($this->once())->method('getMarriageDate')->willReturn(new Date(''));
        $family
            ->expects($this->exactly(2))
            ->method('facts')
            ->with(self::withConsecutive([['MARR'], ['DIV']]))
            ->willReturnOnConsecutiveCalls(
                new Collection([$fact]),
                new Collection([$fact])
            );

        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('M');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = self::createStub(CensusInterface::class);

        $column = new CensusColumnConditionCanadaWidowed($census, '', '');
        $census->method('censusDate')->willReturn('30 JUN 1830');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testDivorcedFemale(): void
    {
        $fact = self::createStub(Fact::class);

        $family = $this->createMock(Family::class);
        $family->expects($this->once())->method('getMarriageDate')->willReturn(new Date(''));
        $family
            ->expects($this->exactly(2))
            ->method('facts')
            ->with(self::withConsecutive([['MARR'], ['DIV']]))
            ->willReturnOnConsecutiveCalls(
                new Collection([$fact]),
                new Collection([$fact])
            );

        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = self::createStub(CensusInterface::class);

        $column = new CensusColumnConditionCanadaWidowed($census, '', '');
        $census->method('censusDate')->willReturn('30 JUN 1830');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testMarriedMale(): void
    {
        $fact = self::createStub(Fact::class);

        $family = $this->createMock(Family::class);
        $family->expects($this->once())->method('getMarriageDate')->willReturn(new Date(''));
        $family
            ->expects($this->exactly(2))
            ->method('facts')
            ->with(self::withConsecutive([['MARR'], ['DIV']]))
            ->willReturnOnConsecutiveCalls(
                new Collection([$fact]),
                new Collection()
            );

        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('M');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = self::createStub(CensusInterface::class);

        $column = new CensusColumnConditionCanadaWidowed($census, '', '');
        $census->method('censusDate')->willReturn('30 JUN 1830');

        self::assertSame('', $column->generate($individual, $individual));
    }

    public function testMarriedFemale(): void
    {
        $fact = self::createStub(Fact::class);

        $family = $this->createMock(Family::class);
        $family->expects($this->once())->method('getMarriageDate')->willReturn(new Date(''));
        $family
            ->expects($this->exactly(2))
            ->method('facts')
            ->with(self::withConsecutive([['MARR'], ['DIV']]))
            ->willReturnOnConsecutiveCalls(
                new Collection([$fact]),
                new Collection()
            );

        $individual = self::createStub(Individual::class);
        $individual->method('sex')->willReturn('F');
        $individual->method('spouseFamilies')->willReturn(new Collection([$family]));

        $census = self::createStub(CensusInterface::class);

        $column = new CensusColumnConditionCanadaWidowed($census, '', '');
        $census->method('censusDate')->willReturn('30 JUN 1830');

        self::assertSame('', $column->generate($individual, $individual));
    }
}
