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

namespace Fisharebest\Webtrees\Tests\Unit\I18N\Languages;

use Fisharebest\Webtrees\Contracts\LanguageInterface;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Factories\FamilyFactory;
use Fisharebest\Webtrees\Factories\IndividualFactory;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\RelationshipService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\DataProvider;

use function array_reverse;

/**
 * Abstract base class for per-language tests.
 *
 * Each concrete subclass returns a language instance
 */
abstract class AbstractLanguageTestCase extends TestCase
{
    /**
     * The language under test.
     */
    abstract protected static function language(): LanguageInterface;

    abstract protected static function expectedDateOrder(): string;

    abstract public function testScript(): void;

    abstract public function testFirstDay(): void;

    abstract public function testPaperSize(): void;

    abstract public function testTextDirection(): void;

    abstract public function testAlphabet(): void;

    abstract public function testLanguageTag(): void;

    abstract public function testEndonym(): void;

    abstract public function testStrtolower(): void;

    abstract public function testStrtoupper(): void;

    abstract public function testDigits(): void;

    abstract public function testNumber(): void;

    abstract public function testPercentage(): void;

    /**
     * Provide date formatting test data for this locale.
     *
     * @return list<array{string,string}>
     */
    abstract public static function dateProvider(): array;

    abstract public function testRelationships(): void;

    #[DataProvider('dateProvider')]
    public function testDateFormatting(string $gedcom, string $expected): void
    {
        $date = new Date($gedcom);
        $actual = static::language()->formatDate($date);

        self::assertSame($expected, $actual, 'DATE: ' . $gedcom);
    }

    public function testDateOrder(): void
    {
        self::assertSame(static::expectedDateOrder(), static::language()->dateOrder());
    }

    // ─── Relationship testing infrastructure ──────────────────────────────

    /**
     * Register empty stub factories so that Individual/Family constructors
     * don't attempt to use real database-backed factories.
     * Must be called BEFORE creating any Individual or Family objects.
     */
    protected static function initFactories(): void
    {
        $individual_factory = self::createStub(IndividualFactory::class);
        $family_factory = self::createStub(FamilyFactory::class);

        Registry::individualFactory($individual_factory);
        Registry::familyFactory($family_factory);
    }

    /**
     * Create a male individual stub.
     */
    protected static function male(string $xref, string $extraGedcom = ''): Individual
    {
        $tree = self::createStub(Tree::class);
        $gedcom = "0 @{$xref}@ INDI\n1 SEX M";
        if ($extraGedcom !== '') {
            $gedcom .= "\n" . $extraGedcom;
        }

        return new Individual($xref, $gedcom, null, $tree);
    }

    /**
     * Create a female individual stub.
     */
    protected static function female(string $xref, string $extraGedcom = ''): Individual
    {
        $tree = self::createStub(Tree::class);
        $gedcom = "0 @{$xref}@ INDI\n1 SEX F";
        if ($extraGedcom !== '') {
            $gedcom .= "\n" . $extraGedcom;
        }

        return new Individual($xref, $gedcom, null, $tree);
    }

    /**
     * Create an unknown-sex individual stub.
     */
    protected static function unknown(string $xref, string $extraGedcom = ''): Individual
    {
        $tree = self::createStub(Tree::class);
        $gedcom = "0 @{$xref}@ INDI\n1 SEX U";
        if ($extraGedcom !== '') {
            $gedcom .= "\n" . $extraGedcom;
        }

        return new Individual($xref, $gedcom, null, $tree);
    }

    /**
     * Create a family stub.
     */
    protected static function family(string $xref, string $gedcom): Family
    {
        $tree = self::createStub(Tree::class);

        return new Family($xref, $gedcom, null, $tree);
    }

    /**
     * Register individuals and families with the factories.
     * Call this AFTER all Individual/Family objects have been created.
     *
     * @param array<Individual> $individuals
     * @param array<Family>     $families
     */
    protected static function registerStubs(array $individuals, array $families): void
    {
        $individual_factory = self::createStub(IndividualFactory::class);
        $family_factory = self::createStub(FamilyFactory::class);

        $individual_map = [];
        foreach ($individuals as $individual) {
            $individual_map[] = [$individual->xref(), $individual];
        }

        $family_map = [];
        foreach ($families as $fam) {
            $family_map[] = [$fam->xref(), $fam];
        }

        $individual_factory->method('make')->willReturnMap($individual_map);
        $family_factory->method('make')->willReturnMap($family_map);

        Registry::individualFactory($individual_factory);
        Registry::familyFactory($family_factory);
    }

    /**
     * Assert that a relationship path produces the expected name.
     *
     * @param string                   $expected The expected relationship name
     * @param array<Family|Individual> $nodes    The path of alternating Individual/Family nodes
     */
    protected static function assertRelationshipName(string $expected, array $nodes): void
    {
        $service = new RelationshipService();
        $actual = $service->nameFromPath($nodes, static::language());
        $path = implode('-', array_map(static fn (GedcomRecord $record): string => $record->xref(), $nodes));
        $message = 'Path: ' . $path;

        self::assertSame($expected, $actual, $message);
    }

    /**
     * Assert relationship names in both directions.
     *
     * @param string                   $forward Expected name in forward direction
     * @param string                   $reverse Expected name in reverse direction
     * @param array<Family|Individual> $nodes   The path nodes
     */
    protected static function assertRelationshipNames(string $forward, string $reverse, array $nodes): void
    {
        static::assertRelationshipName($forward, $nodes);
        static::assertRelationshipName($reverse, array_reverse($nodes));
    }
}
