<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PolishSurnameTradition::class)]
#[CoversClass(PatrilinealSurnameTradition::class)]
class PolishSurnameTraditionTest extends TestCase
{
    private SurnameTraditionInterface $surname_tradition;

    /**
     * Test whether surnames are used
     */
    public function testSurnames(): void
    {
        self::assertSame('//', $this->surname_tradition->defaultName());
    }

    /**
     * Test new son names
     */
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

    /**
     * Test new daughter names
     */
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

    /**
     * Test new daughter names
     */
    public function testNewDaughterNamesInflected(): void
    {
        $father_fact = $this->createMock(Fact::class);
        $father_fact->method('value')->willReturn('John /Whitecki/');

        $father = $this->createMock(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createMock(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /Black/');

        $mother = $this->createMock(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /Whitecka/\n2 TYPE BIRTH\n2 SURN Whitecki"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        $father_fact = $this->createMock(Fact::class);
        $father_fact->method('value')->willReturn('John /Whitedzki/');

        $father = $this->createMock(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createMock(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /Black/');

        $mother = $this->createMock(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /Whitedzka/\n2 TYPE BIRTH\n2 SURN Whitedzki"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        $father_fact = $this->createMock(Fact::class);
        $father_fact->method('value')->willReturn('John /Whiteski/');

        $father = $this->createMock(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createMock(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /Black/');

        $mother = $this->createMock(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /Whiteska/\n2 TYPE BIRTH\n2 SURN Whiteski"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        $father_fact = $this->createMock(Fact::class);
        $father_fact->method('value')->willReturn('John /Whiteżki/');

        $father = $this->createMock(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createMock(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /Black/');

        $mother = $this->createMock(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /Whiteżka/\n2 TYPE BIRTH\n2 SURN Whiteżki"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );
    }

    /**
     * Test new child names
     */
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

    /**
     * Test new child names
     */
    public function testNewChildNamesWithNoParentsNames(): void
    {
        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newChildNames(null, null, 'U')
        );
    }

    /**
     * Test new father names
     */
    public function testNewFatherNames(): void
    {
        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('Chris /White/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /White/\n2 TYPE BIRTH\n2 SURN White"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );
    }

    /**
     * Test new father names
     */
    public function testNewFatherNamesInflected(): void
    {
        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('Chris /Whitecka/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Whitecki/\n2 TYPE BIRTH\n2 SURN Whitecki"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );

        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('Chris /Whitedzka/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Whitedzki/\n2 TYPE BIRTH\n2 SURN Whitedzki"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );

        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('Chris /Whiteska/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Whiteski/\n2 TYPE BIRTH\n2 SURN Whiteski"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );

        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('Chris /Whiteżka/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Whiteżki/\n2 TYPE BIRTH\n2 SURN Whiteżki"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );
    }

    /**
     * Test new mother names
     */
    public function testNewMotherNames(): void
    {
        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('Chris /White/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newParentNames($individual, 'F')
        );
    }

    /**
     * Test new parent names
     */
    public function testNewParentNames(): void
    {
        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('Chris /White/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newParentNames($individual, 'U')
        );
    }

    /**
     * Test new spouse names
     */
    public function testNewSpouseNames(): void
    {
        $fact = $this->createMock(Fact::class);
        $fact->method('value')->willReturn('Chris /White/');

        $individual = $this->createMock(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newSpouseNames($individual, 'M')
        );

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH", "1 NAME /White/\n2 TYPE MARRIED\n2 SURN White"],
            $this->surname_tradition->newSpouseNames($individual, 'F')
        );

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newSpouseNames($individual, 'U')
        );
    }

    /**
     * Prepare the environment for these tests
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->surname_tradition = new PolishSurnameTradition();
    }
}
