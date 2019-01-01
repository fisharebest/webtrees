<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Mockery;

/**
 * Test harness for the class CensusColumnConditionFrenchVeuve
 */
class CensusColumnConditionFrenchVeuveTest extends \Fisharebest\Webtrees\TestCase
{
    /**
     * Delete mock objects
     *
     * @return void
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuve
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testNoSpouseFamiliesMale()
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1800'));

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $column = new CensusColumnConditionFrenchVeuve($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuve
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testNoSpouseFamiliesFemale()
    {
        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('F');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1800'));

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $column = new CensusColumnConditionFrenchVeuve($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuve
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testNoFamilyFactsMale()
    {
        $family = Mockery::mock(Family::class);
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('facts')->with(['MARR'])->andReturn([]);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1800'));
        $individual->shouldReceive('getSex')->andReturn('M');

        $census = Mockery::mock(CensusInterface::class);

        $column = new CensusColumnConditionFrenchVeuve($census, '', '');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuve
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testNoFamilyFactsFemale()
    {
        $family = Mockery::mock(Family::class);
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('facts')->with(['MARR'])->andReturn([]);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1800'));
        $individual->shouldReceive('getSex')->andReturn('F');

        $census = Mockery::mock(CensusInterface::class);

        $column = new CensusColumnConditionFrenchVeuve($census, '', '');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuve
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testSpouseDeadMale()
    {
        $fact = Mockery::mock(Fact::class);

        $spouse = Mockery::mock(Individual::class);
        $spouse->shouldReceive('getDeathDate')->andReturn(new Date('1820'));

        $family = Mockery::mock(Family::class);
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('facts')->with(['MARR'])->andReturn([$fact]);
        $family->shouldReceive('facts')->with(['DIV'])->andReturn([]);
        $family->shouldReceive('getSpouse')->andReturn($spouse);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock(CensusInterface::class);

        $column = new CensusColumnConditionFrenchVeuve($census, '', '');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuve
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testSpouseDeadFemale()
    {
        $fact = Mockery::mock(Fact::class);

        $spouse = Mockery::mock(Individual::class);
        $spouse->shouldReceive('getDeathDate')->andReturn(new Date('1820'));

        $family = Mockery::mock(Family::class);
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('facts')->with(['MARR'])->andReturn([$fact]);
        $family->shouldReceive('facts')->with(['DIV'])->andReturn([]);
        $family->shouldReceive('getSpouse')->andReturn($spouse);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('F');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock(CensusInterface::class);

        $column = new CensusColumnConditionFrenchVeuve($census, '', '');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $this->assertSame('1', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuve
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testNoFamilyUnmarriedMale()
    {
        $family = Mockery::mock(Family::class);
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('facts')->with(['MARR'])->andReturn([]);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1800'));

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $column = new CensusColumnConditionFrenchVeuve($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuve
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testNoFamilyUnmarriedFemale()
    {
        $family = Mockery::mock(Family::class);
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('facts')->with(['MARR'])->andReturn([]);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('F');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1800'));

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $column = new CensusColumnConditionFrenchVeuve($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuve
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testChildMale()
    {
        $family = Mockery::mock(Family::class);
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('facts')->with(['MARR'])->andReturn([]);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1820'));

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $column = new CensusColumnConditionFrenchVeuve($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuve
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testChildFemale()
    {
        $family = Mockery::mock(Family::class);
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('facts')->with(['MARR'])->andReturn([]);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('F');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1820'));

        $census = Mockery::mock(CensusInterface::class);
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $column = new CensusColumnConditionFrenchVeuve($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuve
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testDivorcedMale()
    {
        $fact = Mockery::mock(Fact::class);

        $family = Mockery::mock(Family::class);
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('facts')->with(['MARR'])->andReturn([$fact]);
        $family->shouldReceive('facts')->with(['DIV'])->andReturn([$fact]);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock(CensusInterface::class);

        $column = new CensusColumnConditionFrenchVeuve($census, '', '');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionFrenchVeuve
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     *
     * @return void
     */
    public function testDivorcedFemale()
    {
        $fact = Mockery::mock(Fact::class);

        $family = Mockery::mock(Family::class);
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('facts')->with(['MARR'])->andReturn([$fact]);
        $family->shouldReceive('facts')->with(['DIV'])->andReturn([$fact]);

        $individual = Mockery::mock(Individual::class);
        $individual->shouldReceive('getSex')->andReturn('F');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock(CensusInterface::class);

        $column = new CensusColumnConditionFrenchVeuve($census, '', '');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $this->assertSame('', $column->generate($individual, $individual));
    }
}
