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
 * Test harness for the class LithuanianSurnameTraditionTest
 */
class LithuanianSurnameTraditionTest extends TestCase
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

        $this->surname_tradition = new LithuanianSurnameTradition();
    }

    /**
     * Test whether married surnames are used
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
     * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     *
     * @return void
     */
    public function testMarriedSurnames(): void
    {
        self::assertTrue($this->surname_tradition->hasMarriedNames());
    }

    /**
     * Test whether surnames are used
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
     * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     *
     * @return void
     */
    public function testSurnames(): void
    {
        self::assertTrue($this->surname_tradition->hasSurnames());
    }

    /**
     * Test new son names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
     * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     *
     * @return void
     */
    public function testNewSonNames(): void
    {
        $father_fact = $this->createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /White/');

        $father = $this->createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createStub(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /Black/');

        $mother = $this->createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /White/\n2 TYPE birth\n2 SURN White"],
            $this->surname_tradition->newChildNames($father, $mother, 'M')
        );
    }

    /**
     * Test new daughter names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
     * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     *
     * @return void
     */
    public function testNewDaughterNames(): void
    {
        $father_fact = $this->createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /White/');

        $father = $this->createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createStub(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /Black/');

        $mother = $this->createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /White/\n2 TYPE birth\n2 SURN White"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );
    }

    /**
     * Test new daughter names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
     * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     *
     * @return void
     */
    public function testNewDaughterNamesInflected(): void
    {
        $father_fact = $this->createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /Whita/');

        $father = $this->createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createStub(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /Black/');

        $mother = $this->createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /Whitaitė/\n2 TYPE birth\n2 SURN Whita"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        $father_fact = $this->createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /Whitas/');

        $father = $this->createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        self::assertSame(
            ["1 NAME /Whitaitė/\n2 TYPE birth\n2 SURN Whitas"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        $father_fact = $this->createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /Whitis/');

        $father = $this->createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        self::assertSame(
            ["1 NAME /Whitytė/\n2 TYPE birth\n2 SURN Whitis"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        $father_fact = $this->createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /Whitys/');

        $father = $this->createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        self::assertSame(
            ["1 NAME /Whitytė/\n2 TYPE birth\n2 SURN Whitys"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        $father_fact = $this->createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /Whitius/');

        $father = $this->createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        self::assertSame(
            ["1 NAME /Whitiūtė/\n2 TYPE birth\n2 SURN Whitius"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        $father_fact = $this->createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /Whitus/');

        $father = $this->createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        self::assertSame(
            ["1 NAME /Whitutė/\n2 TYPE birth\n2 SURN Whitus"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );
    }

    /**
     * Test new child names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
     * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     *
     * @return void
     */
    public function testNewChildNames(): void
    {
        $father_fact = $this->createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /White/');

        $father = $this->createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createStub(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /Black/');

        $mother = $this->createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /White/\n2 TYPE birth\n2 SURN White"],
            $this->surname_tradition->newChildNames($father, $mother, 'U')
        );
    }

    /**
     * Test new child names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
     * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     *
     * @return void
     */
    public function testNewChildNamesWithNoParentsNames(): void
    {
        self::assertSame(
            ["1 NAME //\n2 TYPE birth"],
            $this->surname_tradition->newChildNames(null, null, 'U')
        );
    }

    /**
     * Test new father names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
     * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     *
     * @return void
     */
    public function testNewFatherNames(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('John /White/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /White/\n2 TYPE birth\n2 SURN White"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );
    }

    /**
     * Test new father names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
     * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     *
     * @return void
     */
    public function testNewFatherNamesInflected(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('Mary /Whitaitė/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Whitas/\n2 TYPE birth\n2 SURN Whitas"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );

        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('Mary /Whitytė/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Whitis/\n2 TYPE birth\n2 SURN Whitis"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );

        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('Mary /Whitiūtė/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Whitius/\n2 TYPE birth\n2 SURN Whitius"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );

        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('Mary /Whitutė/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Whitus/\n2 TYPE birth\n2 SURN Whitus"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );
    }

    /**
     * Test new mother names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
     * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     *
     * @return void
     */
    public function testNewMotherNames(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('John /White/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE birth"],
            $this->surname_tradition->newParentNames($individual, 'F')
        );
    }

    /**
     * Test new parent names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
     * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     *
     * @return void
     */
    public function testNewParentNames(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('John /White/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE birth"],
            $this->surname_tradition->newParentNames($individual, 'U')
        );
    }

    /**
     * Test new husband names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
     * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     *
     * @return void
     */
    public function testNewHusbandNames(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('Mary /Black/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE birth"],
            $this->surname_tradition->newSpouseNames($individual, 'M')
        );
    }

    /**
     * Test new wife names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
     * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     *
     * @return void
     */
    public function testNewWifeNames(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('John /White/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE birth", "1 NAME /White/\n2 TYPE married\n2 SURN White"],
            $this->surname_tradition->newSpouseNames($individual, 'F')
        );
    }

    /**
     * Test new spouse names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
     * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     *
     * @return void
     */
    public function testNewSpouseNames(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('John /White/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE birth"],
            $this->surname_tradition->newSpouseNames($individual, 'U')
        );
    }
}
