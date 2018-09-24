<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use Mockery;

/**
 * Test harness for the class CensusColumnConditionEnglish
 */
class CensusColumnConditionEnglishTest extends \Fisharebest\Webtrees\TestCase
{
    /**
     * Delete mock objects
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionEnglish
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     */
    public function testNoSpouseFamiliesMale()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1800'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $column = new CensusColumnConditionEnglish($census, '', '');

        $this->assertSame('Unm', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionEnglish
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     */
    public function testNoSpouseFamiliesFemale()
    {
        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('F');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1800'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $column = new CensusColumnConditionEnglish($census, '', '');

        $this->assertSame('Unm', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionEnglish
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     */
    public function testNoFamilyFactsMale()
    {
        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('getFacts')->with('MARR')->andReturn([]);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1800'));
        $individual->shouldReceive('getSex')->andReturn('M');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');

        $column = new CensusColumnConditionEnglish($census, '', '');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $this->assertSame('Unm', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionEnglish
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     */
    public function testNoFamilyFactsFemale()
    {
        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('getFacts')->with('MARR')->andReturn([]);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1800'));
        $individual->shouldReceive('getSex')->andReturn('F');

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');

        $column = new CensusColumnConditionEnglish($census, '', '');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $this->assertSame('Unm', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionEnglish
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     */
    public function testSpouseDeadMale()
    {
        $fact = Mockery::mock('Fisharebest\Webtrees\Fact');

        $spouse = Mockery::mock('Fisharebest\Webtrees\Individual');
        $spouse->shouldReceive('getDeathDate')->andReturn(new Date('1820'));

        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('getFacts')->with('MARR')->andReturn([$fact]);
        $family->shouldReceive('getFacts')->with('DIV')->andReturn([]);
        $family->shouldReceive('getSpouse')->andReturn($spouse);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');

        $column = new CensusColumnConditionEnglish($census, '', '');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $this->assertSame('Wid', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionEnglish
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     */
    public function testSpouseDeadFemale()
    {
        $fact = Mockery::mock('Fisharebest\Webtrees\Fact');

        $spouse = Mockery::mock('Fisharebest\Webtrees\Individual');
        $spouse->shouldReceive('getDeathDate')->andReturn(new Date('1820'));

        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('getFacts')->with('MARR')->andReturn([$fact]);
        $family->shouldReceive('getFacts')->with('DIV')->andReturn([]);
        $family->shouldReceive('getSpouse')->andReturn($spouse);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('F');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');

        $column = new CensusColumnConditionEnglish($census, '', '');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $this->assertSame('Wid', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionEnglish
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     */
    public function testNoFamilyUnmarriedMale()
    {
        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('getFacts')->with('MARR')->andReturn([]);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1800'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $column = new CensusColumnConditionEnglish($census, '', '');

        $this->assertSame('Unm', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionEnglish
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     */
    public function testNoFamilyUnmarriedFemale()
    {
        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('getFacts')->with('MARR')->andReturn([]);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('F');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1800'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $column = new CensusColumnConditionEnglish($census, '', '');

        $this->assertSame('Unm', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionEnglish
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     */
    public function testChildMale()
    {
        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('getFacts')->with('MARR')->andReturn([]);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1820'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $column = new CensusColumnConditionEnglish($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionEnglish
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     */
    public function testChildFemale()
    {
        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('getFacts')->with('MARR')->andReturn([]);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('F');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);
        $individual->shouldReceive('getEstimatedBirthDate')->andReturn(new Date('1820'));

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $column = new CensusColumnConditionEnglish($census, '', '');

        $this->assertSame('', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionEnglish
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     */
    public function testDivorcedMale()
    {
        $fact = Mockery::mock('Fisharebest\Webtrees\Fact');

        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('getFacts')->with('MARR')->andReturn([$fact]);
        $family->shouldReceive('getFacts')->with('DIV')->andReturn([$fact]);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('M');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');

        $column = new CensusColumnConditionEnglish($census, '', '');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $this->assertSame('Div', $column->generate($individual, $individual));
    }

    /**
     * @covers \Fisharebest\Webtrees\Census\CensusColumnConditionEnglish
     * @covers \Fisharebest\Webtrees\Census\AbstractCensusColumnCondition
     */
    public function testDivorcedFemale()
    {
        $fact = Mockery::mock('Fisharebest\Webtrees\Fact');

        $family = Mockery::mock('Fisharebest\Webtrees\Family');
        $family->shouldReceive('getMarriageDate')->andReturn(new Date(''));
        $family->shouldReceive('getFacts')->with('MARR')->andReturn([$fact]);
        $family->shouldReceive('getFacts')->with('DIV')->andReturn([$fact]);

        $individual = Mockery::mock('Fisharebest\Webtrees\Individual');
        $individual->shouldReceive('getSex')->andReturn('F');
        $individual->shouldReceive('getSpouseFamilies')->andReturn([$family]);

        $census = Mockery::mock('Fisharebest\Webtrees\Census\CensusInterface');

        $column = new CensusColumnConditionEnglish($census, '', '');
        $census->shouldReceive('censusDate')->andReturn('30 JUN 1830');

        $this->assertSame('Div', $column->generate($individual, $individual));
    }
}
