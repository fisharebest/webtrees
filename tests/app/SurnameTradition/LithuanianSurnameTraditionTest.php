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

namespace Fisharebest\Webtrees\SurnameTradition;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;

/**
 * @covers \Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition
 * @covers \Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
 */
class LithuanianSurnameTraditionTest extends TestCase
{
    private SurnameTraditionInterface $surname_tradition;

    protected function setUp(): void
    {
        parent::setUp();

        $this->surname_tradition = new LithuanianSurnameTradition();
    }

    public function testSurnames(): void
    {
        self::assertSame('//', $this->surname_tradition->defaultName());
    }

    public function testNewSonNames(): void
    {
        $father_fact = $this->createMock(Fact::class);
        $father_fact->method('value')->willReturn('John /White/');

        $father = $this->createMock(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createMock(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /Black/');

        $mother = $this->createMock(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /White/\n2 TYPE BIRTH\n2 SURN White"],
            $this->surname_tradition->newChildNames($father, $mother, 'M')
        );
    }

    public function testNewDaughterNames(): void
    {
        $father_fact = $this->createMock(Fact::class);
        $father_fact->method('value')->willReturn('John /White/');

        $father = $this->createMock(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createMock(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /Black/');

        $mother = $this->createMock(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /White/\n2 TYPE BIRTH\n2 SURN White"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );
    }

    public function testNewDaughterNamesInflected(): void
    {
        $father_fact = $this->createMock(Fact::class);
        $father_fact->method('value')->willReturn('John /Whita/');

        $father = $this->createMock(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createMock(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /Black/');

        $mother = $this->createMock(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /Whitaitė/\n2 TYPE BIRTH\n2 SURN Whita"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        $father_fact = $this->createMock(Fact::class);
        $father_fact->method('value')->willReturn('John /Whitas/');

        $father = $this->createMock(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        self::assertSame(
            ["1 NAME /Whitaitė/\n2 TYPE BIRTH\n2 SURN Whitas"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        $father_fact = $this->createMock(Fact::class);
        $father_fact->method('value')->willReturn('John /Whitis/');

        $father = $this->createMock(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        self::assertSame(
            ["1 NAME /Whitytė/\n2 TYPE BIRTH\n2 SURN Whitis"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        $father_fact = $this->createMock(Fact::class);
        $father_fact->method('value')->willReturn('John /Whitys/');

        $father = $this->createMock(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        self::assertSame(
            ["1 NAME /Whitytė/\n2 TYPE BIRTH\n2 SURN Whitys"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        $father_fact = $this->createMock(Fact::class);
        $father_fact->method('value')->willReturn('John /Whitius/');

        $father = $this->createMock(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        self::assertSame(
            ["1 NAME /Whitiūtė/\n2 TYPE BIRTH\n2 SURN Whitius"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        $father_fact = $this->createMock(Fact::class);
        $father_fact->method('value')->willReturn('John /Whitus/');

        $father = $this->createMock(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        self::assertSame(
            ["1 NAME /Whitutė/\n2 TYPE BIRTH\n2 SURN Whitus"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );
    }

    public function testNewChildNames(): void
    {
        $father_fact = $this->createMock(Fact::class);
        $father_fact->method('value')->willReturn('John /White/');

        $father = $this->createMock(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createMock(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /Black/');

        $mother = $this->createMock(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /White/\n2 TYPE BIRTH\n2 SURN White"],
            $this->surname_tradition->newChildNames($father, $mother, 'U')
        );
    }

    public function testNewChildNamesWithNoParentsNames(): void
    {
        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newChildNames(null, null, 'U')
        );
    }

    public function testNewFatherNames(): void
    {
        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('John /White/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /White/\n2 TYPE BIRTH\n2 SURN White"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );
    }

    public function testNewFatherNamesInflected(): void
    {
        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('Mary /Whitaitė/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Whitas/\n2 TYPE BIRTH\n2 SURN Whitas"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );

        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('Mary /Whitytė/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Whitis/\n2 TYPE BIRTH\n2 SURN Whitis"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );

        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('Mary /Whitiūtė/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Whitius/\n2 TYPE BIRTH\n2 SURN Whitius"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );

        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('Mary /Whitutė/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Whitus/\n2 TYPE BIRTH\n2 SURN Whitus"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );
    }

    public function testNewMotherNames(): void
    {
        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('John /White/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newParentNames($individual, 'F')
        );
    }

    public function testNewParentNames(): void
    {
        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('John /White/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newParentNames($individual, 'U')
        );
    }

    public function testNewHusbandNames(): void
    {
        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('Mary /Black/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newSpouseNames($individual, 'M')
        );
    }

    public function testNewWifeNames(): void
    {
        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('John /White/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH", "1 NAME /White/\n2 TYPE MARRIED\n2 SURN White"],
            $this->surname_tradition->newSpouseNames($individual, 'F')
        );
    }

    public function testNewSpouseNames(): void
    {
        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('John /White/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newSpouseNames($individual, 'U')
        );
    }
}
