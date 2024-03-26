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


#[CoversClass(PaternalSurnameTradition::class)]
class PaternalSurnameTraditionTest extends TestCase
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
     * Test new child names
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
            ["1 NAME /White/\n2 TYPE BIRTH\n2 SURN White"],
            $this->surname_tradition->newChildNames($father, $mother, 'M')
        );

        self::assertSame(
            ["1 NAME /White/\n2 TYPE BIRTH\n2 SURN White"],
            $this->surname_tradition->newChildNames($father, $mother, 'F')
        );

        self::assertSame(
            ["1 NAME /White/\n2 TYPE BIRTH\n2 SURN White"],
            $this->surname_tradition->newChildNames($father, $mother, 'U')
        );
    }

    /**
     * Test new child names
     */
    public function testNewChildNamesWithSpfx(): void
    {
        $father_fact = $this->createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /de White/');

        $father = $this->createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createStub(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /van Black/');

        $mother = $this->createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /de White/\n2 TYPE BIRTH\n2 SPFX de\n2 SURN White"],
            $this->surname_tradition->newChildNames($father, $mother, 'U')
        );
    }

    /**
     * Test new child names
     */
    public function testNewChildNamesWithMultipleSpfx(): void
    {
        $father_fact = $this->createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /van der White/');

        $father = $this->createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createStub(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /van Black/');

        $mother = $this->createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /van der White/\n2 TYPE BIRTH\n2 SPFX van der\n2 SURN White"],
            $this->surname_tradition->newChildNames($father, $mother, 'U')
        );
    }

    /**
     * Test new child names
     */
    public function testNewChildNamesWithDutchSpfx(): void
    {
        $father_fact = $this->createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /\'t White/');

        $father = $this->createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createStub(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /van Black/');

        $mother = $this->createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /'t White/\n2 TYPE BIRTH\n2 SPFX 't\n2 SURN White"],
            $this->surname_tradition->newChildNames($father, $mother, 'U')
        );
    }

    /**
     * Test new child names
     */
    public function testNewChildNamesWithMultipleDutchSpfx(): void
    {
        $father_fact = $this->createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /van \'t White/');

        $father = $this->createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = $this->createStub(Fact::class);
        $mother_fact->method('value')->willReturn('Mary /van Black/');

        $mother = $this->createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /van 't White/\n2 TYPE BIRTH\n2 SPFX van 't\n2 SURN White"],
            $this->surname_tradition->newChildNames($father, $mother, 'U')
        );
    }

    /**
     * Test new father names
     */
    public function testNewFatherNames(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('Chris /White/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /White/\n2 TYPE BIRTH\n2 SURN White"],
            $this->surname_tradition->newParentNames($individual, 'M')
        );
    }

    /**
     * Test new mother names
     */
    public function testNewMotherNames(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('Chris /White/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH", "1 NAME /White/\n2 TYPE MARRIED\n2 SURN White"],
            $this->surname_tradition->newParentNames($individual, 'F')
        );
    }

    /**
     * Test new parent names
     */
    public function testNewParentNames(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('Chris /White/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newParentNames($individual, 'U')
        );
    }

    /**
     * Test new husband names
     */
    public function testNewHusbandNames(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('Chris /White/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newSpouseNames($individual, 'M')
        );
    }

    /**
     * Test new wife names
     */
    public function testNewWifeNames(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('Chris /White/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH", "1 NAME /White/\n2 TYPE MARRIED\n2 SURN White"],
            $this->surname_tradition->newSpouseNames($individual, 'F')
        );
    }

    /**
     * Test new wife names
     */
    public function testNewWifeNamesWithSpfx(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('Chris /van der White/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH", "1 NAME /van der White/\n2 TYPE MARRIED\n2 SPFX van der\n2 SURN White"],
            $this->surname_tradition->newSpouseNames($individual, 'F')
        );
    }

    /**
     * Test new spouse names
     */
    public function testNewSpouseNames(): void
    {
        $fact = $this->createStub(Fact::class);
        $fact->method('value')->willReturn('Chris /White/');

        $individual = $this->createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

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

        $this->surname_tradition = new PaternalSurnameTradition();
    }
}
