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

namespace Fisharebest\Webtrees\SurnameTradition;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;

/**
 * Test harness for the class PortugueseSurnameTradition
 */
class PortugueseSurnameTraditionTest extends TestCase
{
    private SurnameTraditionInterface $surname_tradition;

    /**
     * Prepare the environment for these tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->surname_tradition = new PortugueseSurnameTradition();
    }

    /**
     * Test whether married surnames are used
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PortugueseSurnameTradition
     *
     * @return void
     */
    public function testMarriedSurnames(): void
    {
        self::assertFalse($this->surname_tradition->hasMarriedNames());
    }

    /**
     * Test whether surnames are used
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PortugueseSurnameTradition
     *
     * @return void
     */
    public function testSurnames(): void
    {
        self::assertTrue($this->surname_tradition->hasSurnames());
    }

    /**
     * Test new child names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PortugueseSurnameTradition
     *
     * @return void
     */
    public function testNewChildNames(): void
    {
        $father_fact = $this->createStub(Fact::class);
        $father_fact->expects(self::any())->method('value')->willReturn('Gabriel /Garcia/ /Iglesias/');

        $father = $this->createStub(Individual::class);
        $father->expects(self::any())->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createStub(Fact::class);
        $mother_fact->expects(self::any())->method('value')->willReturn('Maria /Ruiz/ /Lorca/');

        $mother = $this->createStub(Individual::class);
        $mother->expects(self::any())->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /Iglesias/ /Lorca/\n2 TYPE birth\n2 SURN Iglesias,Lorca"],
            $this->surname_tradition->newChildNames($father, $mother, 'M')
        );

        self::assertSame(
            ["1 NAME /Iglesias/ /Lorca/\n2 TYPE birth\n2 SURN Iglesias,Lorca"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        self::assertSame(
            ["1 NAME /Iglesias/ /Lorca/\n2 TYPE birth\n2 SURN Iglesias,Lorca"],
            $this->surname_tradition->newChildNames($father, $mother, 'U')
        );
    }

    /**
     * Test new child names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PortugueseSurnameTradition
     *
     * @return void
     */
    public function testNewChildNamesWithNoParentsNames(): void
    {
        self::assertSame(
            ["1 NAME // //\n2 TYPE birth"],
            $this->surname_tradition->newChildNames(null, null, 'U')
        );
    }

    /**
     * Test new child names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PortugueseSurnameTradition
     *
     * @return void
     */
    public function testNewChildNamesCompunds(): void
    {
        $father_fact = $this->createStub(Fact::class);
        $father_fact->expects(self::any())->method('value')->willReturn('Gabriel /Garcia/ y /Iglesias/');

        $father = $this->createStub(Individual::class);
        $father->expects(self::any())->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createStub(Fact::class);
        $mother_fact->expects(self::any())->method('value')->willReturn('Maria /Ruiz/ y /Lorca/');

        $mother = $this->createStub(Individual::class);
        $mother->expects(self::any())->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /Iglesias/ /Lorca/\n2 TYPE birth\n2 SURN Iglesias,Lorca"],
            $this->surname_tradition->newChildNames($father, $mother, 'M')
        );
    }

    /**
     * Test new parent names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PortugueseSurnameTradition
     *
     * @return void
     */
    public function testNewParentNames(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->expects(self::any())->method('value')->willReturn('Gabriel /Garcia/ /Iglesias/');

        $individual = $this->createStub(Individual::class);
        $individual->expects(self::any())->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME // /Garcia/\n2 TYPE birth\n2 SURN Garcia"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );
        self::assertSame(
            ["1 NAME // /Iglesias/\n2 TYPE birth\n2 SURN Iglesias"],
            $this->surname_tradition->newParentNames($individual, 'F')
        );

        self::assertSame(
            ["1 NAME // //\n2 TYPE birth"],
            $this->surname_tradition->newParentNames($individual, 'U')
        );
    }

    /**
     * Test new spouse names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PortugueseSurnameTradition
     *
     * @return void
     */
    public function testNewSpouseNames(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->expects(self::any())->method('value')->willReturn('Gabriel /Garcia/ /Iglesias/');

        $individual = $this->createStub(Individual::class);
        $individual->expects(self::any())->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME // //\n2 TYPE birth"],
            $this->surname_tradition->newSpouseNames($individual, 'M')
        );

        self::assertSame(
            ["1 NAME // //\n2 TYPE birth"],
            $this->surname_tradition->newSpouseNames($individual, 'F')
        );

        self::assertSame(
            ["1 NAME // //\n2 TYPE birth"],
            $this->surname_tradition->newSpouseNames($individual, 'U')
        );
    }
}
