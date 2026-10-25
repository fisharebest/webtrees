<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Tests\Unit\SurnameTradition;

use Fisharebest\Webtrees\Enums\Sex;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\SurnameTradition\GreekSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\SurnameTraditionInterface;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GreekSurnameTradition::class)]
#[CoversClass(PatrilinealSurnameTradition::class)]
class GreekSurnameTraditionTest extends TestCase
{
    private SurnameTraditionInterface $surname_tradition;

    public function testSurnames(): void
    {
        self::assertSame('//', $this->surname_tradition->defaultName());
    }

    public function testNewSonNames(): void
    {
        $father_fact = self::createStub(Fact::class);
        $father_fact->method('value')->willReturn('Ιωάννης /Παπαδόπουλος/');

        $father = self::createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = self::createStub(Fact::class);
        $mother_fact->method('value')->willReturn('Μαρία /Οικονόμου/');

        $mother = self::createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /Παπαδόπουλος/\n2 TYPE BIRTH\n2 SURN Παπαδόπουλος"],
            $this->surname_tradition->newChildNames($father, $mother, Sex::Male)
        );
    }

    public function testNewDaughterNames(): void
    {
        $father_fact = self::createStub(Fact::class);
        $father_fact->method('value')->willReturn('Ιωάννης /Παπαδόπουλος/');

        $father = self::createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = self::createStub(Fact::class);
        $mother_fact->method('value')->willReturn('Μαρία /Οικονόμου/');

        $mother = self::createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /Παπαδοπούλου/\n2 TYPE BIRTH\n2 SURN Παπαδόπουλος"],
            $this->surname_tradition->newChildNames($father, $mother, Sex::Female)
        );
    }

    public function testNewDaughterNamesInflectedOs(): void
    {
        $father_fact = self::createStub(Fact::class);
        $father_fact->method('value')->willReturn('Νίκος /Οικονόμος/');

        $father = self::createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother = self::createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([]));

        self::assertSame(
            ["1 NAME /Οικονόμου/\n2 TYPE BIRTH\n2 SURN Οικονόμος"],
            $this->surname_tradition->newChildNames($father, $mother, Sex::Female)
        );
    }

    public function testNewDaughterNamesInflectedIs(): void
    {
        $father_fact = self::createStub(Fact::class);
        $father_fact->method('value')->willReturn('Γιώργος /Παπαδάκης/');

        $father = self::createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother = self::createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([]));

        self::assertSame(
            ["1 NAME /Παπαδάκη/\n2 TYPE BIRTH\n2 SURN Παπαδάκης"],
            $this->surname_tradition->newChildNames($father, $mother, Sex::Female)
        );
    }

    public function testNewDaughterNamesInflectedAs(): void
    {
        $father_fact = self::createStub(Fact::class);
        $father_fact->method('value')->willReturn('Δημήτρης /Καρράς/');

        $father = self::createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother = self::createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([]));

        self::assertSame(
            ["1 NAME /Καρρά/\n2 TYPE BIRTH\n2 SURN Καρράς"],
            $this->surname_tradition->newChildNames($father, $mother, Sex::Female)
        );
    }

    public function testNewDaughterNamesInflectedIdis(): void
    {
        $father_fact = self::createStub(Fact::class);
        $father_fact->method('value')->willReturn('Κώστας /Κωνσταντινίδης/');

        $father = self::createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother = self::createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([]));

        self::assertSame(
            ["1 NAME /Κωνσταντινίδου/\n2 TYPE BIRTH\n2 SURN Κωνσταντινίδης"],
            $this->surname_tradition->newChildNames($father, $mother, Sex::Female)
        );
    }

    public function testNewDaughterNamesInflectedAdis(): void
    {
        $father_fact = self::createStub(Fact::class);
        $father_fact->method('value')->willReturn('Σταύρος /Αθανασιάδης/');

        $father = self::createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother = self::createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([]));

        self::assertSame(
            ["1 NAME /Αθανασιάδου/\n2 TYPE BIRTH\n2 SURN Αθανασιάδης"],
            $this->surname_tradition->newChildNames($father, $mother, Sex::Female)
        );
    }

    public function testNewDaughterNamesInflectedLatinOpoulos(): void
    {
        $father_fact = self::createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /Papadopoulos/');

        $father = self::createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother = self::createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([]));

        self::assertSame(
            ["1 NAME /Papadopoulou/\n2 TYPE BIRTH\n2 SURN Papadopoulos"],
            $this->surname_tradition->newChildNames($father, $mother, Sex::Female)
        );
    }

    public function testNewDaughterNamesInflectedLatinAkis(): void
    {
        $father_fact = self::createStub(Fact::class);
        $father_fact->method('value')->willReturn('George /Papadakis/');

        $father = self::createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother = self::createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([]));

        self::assertSame(
            ["1 NAME /Papadaki/\n2 TYPE BIRTH\n2 SURN Papadakis"],
            $this->surname_tradition->newChildNames($father, $mother, Sex::Female)
        );
    }

    public function testNewDaughterNamesInflectedLatinIdis(): void
    {
        $father_fact = self::createStub(Fact::class);
        $father_fact->method('value')->willReturn('Kostas /Konstantinidis/');

        $father = self::createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother = self::createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([]));

        self::assertSame(
            ["1 NAME /Konstantinidou/\n2 TYPE BIRTH\n2 SURN Konstantinidis"],
            $this->surname_tradition->newChildNames($father, $mother, Sex::Female)
        );
    }

    public function testNewChildNames(): void
    {
        $father_fact = self::createStub(Fact::class);
        $father_fact->method('value')->willReturn('Ιωάννης /Παπαδόπουλος/');

        $father = self::createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother_fact = self::createStub(Fact::class);
        $mother_fact->method('value')->willReturn('Μαρία /Οικονόμου/');

        $mother = self::createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([$mother_fact]));

        self::assertSame(
            ["1 NAME /Παπαδόπουλος/\n2 TYPE BIRTH\n2 SURN Παπαδόπουλος"],
            $this->surname_tradition->newChildNames($father, $mother, Sex::Unknown)
        );
    }

    public function testNewChildNamesWithNoParentsNames(): void
    {
        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newChildNames(null, null, Sex::Unknown)
        );
    }

    public function testNewFatherNames(): void
    {
        $fact = self::createStub(Fact::class);
        $fact->method('value')->willReturn('Ελένη /Παπαδοπούλου/');

        $individual = self::createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Παπαδόπουλος/\n2 TYPE BIRTH\n2 SURN Παπαδόπουλος"],
            $this->surname_tradition->newParentNames($individual, Sex::Male)
        );
    }

    public function testNewFatherNamesFromDaughterIs(): void
    {
        $fact = self::createStub(Fact::class);
        $fact->method('value')->willReturn('Μαρία /Παπαδάκη/');

        $individual = self::createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Παπαδάκης/\n2 TYPE BIRTH\n2 SURN Παπαδάκης"],
            $this->surname_tradition->newParentNames($individual, Sex::Male)
        );
    }

    public function testNewFatherNamesFromDaughterAs(): void
    {
        $fact = self::createStub(Fact::class);
        $fact->method('value')->willReturn('Αγγελική /Καρρά/');

        $individual = self::createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Καρράς/\n2 TYPE BIRTH\n2 SURN Καρράς"],
            $this->surname_tradition->newParentNames($individual, Sex::Male)
        );
    }

    public function testNewFatherNamesFromDaughterIdou(): void
    {
        $fact = self::createStub(Fact::class);
        $fact->method('value')->willReturn('Σοφία /Κωνσταντινίδου/');

        $individual = self::createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Κωνσταντινίδης/\n2 TYPE BIRTH\n2 SURN Κωνσταντινίδης"],
            $this->surname_tradition->newParentNames($individual, Sex::Male)
        );
    }

    public function testNewFatherNamesInflectedLatin(): void
    {
        $fact = self::createStub(Fact::class);
        $fact->method('value')->willReturn('Helen /Papadopoulou/');

        $individual = self::createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME /Papadopoulos/\n2 TYPE BIRTH\n2 SURN Papadopoulos"],
            $this->surname_tradition->newParentNames($individual, Sex::Male)
        );
    }

    public function testNewMotherNames(): void
    {
        $fact = self::createStub(Fact::class);
        $fact->method('value')->willReturn('Ιωάννης /Παπαδόπουλος/');

        $individual = self::createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newParentNames($individual, Sex::Female)
        );
    }

    public function testNewParentNames(): void
    {
        $fact = self::createStub(Fact::class);
        $fact->method('value')->willReturn('Ιωάννης /Παπαδόπουλος/');

        $individual = self::createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newParentNames($individual, Sex::Unknown)
        );
    }

    public function testNewSpouseNames(): void
    {
        $fact = self::createStub(Fact::class);
        $fact->method('value')->willReturn('Ιωάννης /Παπαδόπουλος/');

        $individual = self::createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newSpouseNames($individual, Sex::Male)
        );

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH", "1 NAME /Παπαδοπούλου/\n2 TYPE MARRIED\n2 SURN Παπαδόπουλος"],
            $this->surname_tradition->newSpouseNames($individual, Sex::Female)
        );

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH"],
            $this->surname_tradition->newSpouseNames($individual, Sex::Unknown)
        );
    }

    public function testNewSpouseNamesInflectedIs(): void
    {
        $fact = self::createStub(Fact::class);
        $fact->method('value')->willReturn('Γιώργος /Παπαδάκης/');

        $individual = self::createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH", "1 NAME /Παπαδάκη/\n2 TYPE MARRIED\n2 SURN Παπαδάκης"],
            $this->surname_tradition->newSpouseNames($individual, Sex::Female)
        );
    }

    public function testNewSpouseNamesInflectedLatinAkis(): void
    {
        $fact = self::createStub(Fact::class);
        $fact->method('value')->willReturn('George /Papadakis/');

        $individual = self::createStub(Individual::class);
        $individual->method('facts')->willReturn(new Collection([$fact]));

        self::assertSame(
            ["1 NAME //\n2 TYPE BIRTH", "1 NAME /Papadaki/\n2 TYPE MARRIED\n2 SURN Papadakis"],
            $this->surname_tradition->newSpouseNames($individual, Sex::Female)
        );
    }

    public function testNewChildNamesWithNonInflectableSurname(): void
    {
        $father_fact = self::createStub(Fact::class);
        $father_fact->method('value')->willReturn('John /White/');

        $father = self::createStub(Individual::class);
        $father->method('facts')->willReturn(new Collection([$father_fact]));

        $mother = self::createStub(Individual::class);
        $mother->method('facts')->willReturn(new Collection([]));

        self::assertSame(
            ["1 NAME /White/\n2 TYPE BIRTH\n2 SURN White"],
            $this->surname_tradition->newChildNames($father, $mother, Sex::Female)
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->surname_tradition = new GreekSurnameTradition();
    }
}
