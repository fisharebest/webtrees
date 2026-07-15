<?php

declare(strict_types=1);

namespace Fisharebest\Webtrees\Tests\Unit;

use Fisharebest\Webtrees\Factories\FamilyFactory;
use Fisharebest\Webtrees\Factories\IndividualFactory;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Relationship::class)]
class RelationshipArchitectureTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testFixedWithGenitiveFemale(): void
    {
        $tree = self::createStub(Tree::class);
        $individual_factory = self::createStub(IndividualFactory::class);
        $family_factory = self::createStub(FamilyFactory::class);
        Registry::familyFactory($family_factory);
        Registry::individualFactory($individual_factory);
        $self = new Individual("i1", "0 @i1@ INDI\n1 SEX M\n1 FAMC @f1@", null, $tree);
        $brother = new Individual("i2", "0 @i2@ INDI\n1 SEX M\n1 FAMC @f1@", null, $tree);
        $family = new Family("f1", "0 @f1@ FAM\n1 CHIL @i1@\n1 CHIL @i2@", null, $tree);
        $individual_factory->method("make")->willReturnMap([["i1", $self], ["i2", $brother]]);
        $family_factory->method("make")->willReturnMap([["f1", $family]]);
        $rel = Relationship::fixed("brother", "%s i brother", "%s e brother")->brother();
        $result = $rel->match([$self, $family, $brother], ["bro"]);
        self::assertNotNull($result);
        self::assertCount(3, $result);
        self::assertSame("%s e brother", $result[2]);
    }

    public function testFixedBackwardCompat(): void
    {
        $tree = self::createStub(Tree::class);
        $individual_factory = self::createStub(IndividualFactory::class);
        $family_factory = self::createStub(FamilyFactory::class);
        Registry::familyFactory($family_factory);
        Registry::individualFactory($individual_factory);
        $self = new Individual("i1", "0 @i1@ INDI\n1 SEX M\n1 FAMC @f1@", null, $tree);
        $brother = new Individual("i2", "0 @i2@ INDI\n1 SEX M\n1 FAMC @f1@", null, $tree);
        $family = new Family("f1", "0 @f1@ FAM\n1 CHIL @i1@\n1 CHIL @i2@", null, $tree);
        $individual_factory->method("make")->willReturnMap([["i1", $self], ["i2", $brother]]);
        $family_factory->method("make")->willReturnMap([["f1", $family]]);
        $rel = Relationship::fixed("brother", "%s of brother")->brother();
        $result = $rel->match([$self, $family, $brother], ["bro"]);
        self::assertNotNull($result);
        self::assertCount(2, $result);
    }

    public function testSelfFemaleMatches(): void
    {
        $tree = self::createStub(Tree::class);
        $individual_factory = self::createStub(IndividualFactory::class);
        $family_factory = self::createStub(FamilyFactory::class);
        Registry::familyFactory($family_factory);
        Registry::individualFactory($individual_factory);
        $self = new Individual("i1", "0 @i1@ INDI\n1 SEX F\n1 FAMC @f1@\n1 BIRT\n2 DATE 2001", null, $tree);
        $brother = new Individual("i2", "0 @i2@ INDI\n1 SEX M\n1 FAMC @f1@\n1 BIRT\n2 DATE 2000", null, $tree);
        $family = new Family("f1", "0 @f1@ FAM\n1 CHIL @i1@\n1 CHIL @i2@", null, $tree);
        $individual_factory->method("make")->willReturnMap([["i1", $self], ["i2", $brother]]);
        $family_factory->method("make")->willReturnMap([["f1", $family]]);
        $rel = Relationship::fixed("oppa", "%s of oppa")->selfFemale()->older()->brother();
        $result = $rel->match([$self, $family, $brother], ["bro"]);
        self::assertNotNull($result);
        self::assertSame("oppa", $result[0]);
    }

    public function testSelfFemaleFails(): void
    {
        $tree = self::createStub(Tree::class);
        $individual_factory = self::createStub(IndividualFactory::class);
        $family_factory = self::createStub(FamilyFactory::class);
        Registry::familyFactory($family_factory);
        Registry::individualFactory($individual_factory);
        $self = new Individual("i1", "0 @i1@ INDI\n1 SEX M\n1 FAMC @f1@\n1 BIRT\n2 DATE 2001", null, $tree);
        $brother = new Individual("i2", "0 @i2@ INDI\n1 SEX M\n1 FAMC @f1@\n1 BIRT\n2 DATE 2000", null, $tree);
        $family = new Family("f1", "0 @f1@ FAM\n1 CHIL @i1@\n1 CHIL @i2@", null, $tree);
        $individual_factory->method("make")->willReturnMap([["i1", $self], ["i2", $brother]]);
        $family_factory->method("make")->willReturnMap([["f1", $family]]);
        $rel = Relationship::fixed("oppa", "%s of oppa")->selfFemale()->older()->brother();
        $result = $rel->match([$self, $family, $brother], ["bro"]);
        self::assertNull($result);
    }

    public function testOlderMultiStep(): void
    {
        $tree = self::createStub(Tree::class);
        $individual_factory = self::createStub(IndividualFactory::class);
        $family_factory = self::createStub(FamilyFactory::class);
        Registry::familyFactory($family_factory);
        Registry::individualFactory($individual_factory);
        $self = new Individual("i1", "0 @i1@ INDI\n1 SEX M\n1 FAMC @f1@\n1 BIRT\n2 DATE 2001", null, $tree);
        $father = new Individual("i2", "0 @i2@ INDI\n1 SEX M\n1 FAMS @f1@\n1 FAMC @f2@", null, $tree);
        $uncle = new Individual("i3", "0 @i3@ INDI\n1 SEX M\n1 FAMS @f3@\n1 FAMC @f2@", null, $tree);
        $cousin = new Individual("i4", "0 @i4@ INDI\n1 SEX M\n1 FAMC @f3@\n1 BIRT\n2 DATE 2000", null, $tree);
        $f1 = new Family("f1", "0 @f1@ FAM\n1 HUSB @i2@\n1 CHIL @i1@", null, $tree);
        $f2 = new Family("f2", "0 @f2@ FAM\n1 CHIL @i2@\n1 CHIL @i3@", null, $tree);
        $f3 = new Family("f3", "0 @f3@ FAM\n1 HUSB @i3@\n1 CHIL @i4@", null, $tree);
        $individual_factory->method("make")->willReturnMap([["i1", $self], ["i2", $father], ["i3", $uncle], ["i4", $cousin]]);
        $family_factory->method("make")->willReturnMap([["f1", $f1], ["f2", $f2], ["f3", $f3]]);
        $rel = Relationship::fixed("older cousin", "%s of older cousin")->older()->father()->brother()->son();
        $result = $rel->match([$self, $f1, $father, $f2, $uncle, $f3, $cousin], ["fat", "bro", "son"]);
        self::assertNotNull($result);
        self::assertSame("older cousin", $result[0]);
    }

    public function testOlderMultiStepFails(): void
    {
        $tree = self::createStub(Tree::class);
        $individual_factory = self::createStub(IndividualFactory::class);
        $family_factory = self::createStub(FamilyFactory::class);
        Registry::familyFactory($family_factory);
        Registry::individualFactory($individual_factory);
        $self = new Individual("i1", "0 @i1@ INDI\n1 SEX M\n1 FAMC @f1@\n1 BIRT\n2 DATE 2000", null, $tree);
        $father = new Individual("i2", "0 @i2@ INDI\n1 SEX M\n1 FAMS @f1@\n1 FAMC @f2@", null, $tree);
        $uncle = new Individual("i3", "0 @i3@ INDI\n1 SEX M\n1 FAMS @f3@\n1 FAMC @f2@", null, $tree);
        $cousin = new Individual("i4", "0 @i4@ INDI\n1 SEX M\n1 FAMC @f3@\n1 BIRT\n2 DATE 2001", null, $tree);
        $f1 = new Family("f1", "0 @f1@ FAM\n1 HUSB @i2@\n1 CHIL @i1@", null, $tree);
        $f2 = new Family("f2", "0 @f2@ FAM\n1 CHIL @i2@\n1 CHIL @i3@", null, $tree);
        $f3 = new Family("f3", "0 @f3@ FAM\n1 HUSB @i3@\n1 CHIL @i4@", null, $tree);
        $individual_factory->method("make")->willReturnMap([["i1", $self], ["i2", $father], ["i3", $uncle], ["i4", $cousin]]);
        $family_factory->method("make")->willReturnMap([["f1", $f1], ["f2", $f2], ["f3", $f3]]);
        $rel = Relationship::fixed("older cousin", "%s of older cousin")->older()->father()->brother()->son();
        $result = $rel->match([$self, $f1, $father, $f2, $uncle, $f3, $cousin], ["fat", "bro", "son"]);
        self::assertNull($result);
    }
}
