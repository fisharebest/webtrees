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

namespace Fisharebest\Webtrees\Tests\Unit\Services;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\FactSortService;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function array_map;

#[CoversClass(FactSortService::class)]
class FactSortServiceTest extends TestCase
{
    protected static bool $uses_database = true;

    private FactSortService $fact_sort_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fact_sort_service = new FactSortService();
    }

    public function testEmptyCollection(): void
    {
        $sorted = $this->fact_sort_service->sort(new Collection());

        self::assertSame([], $this->ids($sorted));
    }

    public function testSingleFact(): void
    {
        $individual = $this->stubIndividual();
        $birth = new Fact("1 BIRT\n2 DATE 1 JAN 1950", $individual, 'birth');

        $sorted = $this->fact_sort_service->sort(new Collection([$birth]));

        self::assertSame(['birth'], $this->ids($sorted));
    }

    // =========================================================================
    // Individual facts only — all dated
    // =========================================================================

    public function testDatedIndividualFactsSortChronologically(): void
    {
        $individual = $this->stubIndividual();

        $death = new Fact("1 DEAT\n2 DATE 1 JAN 2000", $individual, 'death');
        $birth = new Fact("1 BIRT\n2 DATE 1 JAN 1950", $individual, 'birth');

        $sorted = $this->fact_sort_service->sort(new Collection([$death, $birth]));

        self::assertSame(['birth', 'death'], $this->ids($sorted));
    }

    public function testSameDateSortsByType(): void
    {
        $individual = $this->stubIndividual();

        $death = new Fact("1 DEAT\n2 DATE 1 JAN 1950", $individual, 'death');
        $birth = new Fact("1 BIRT\n2 DATE 1 JAN 1950", $individual, 'birth');

        $sorted = $this->fact_sort_service->sort(new Collection([$death, $birth]));

        self::assertSame(['birth', 'death'], $this->ids($sorted));
    }

    public function testSameDateSameTypePreservesOrder(): void
    {
        $individual = $this->stubIndividual();

        $resi1 = new Fact("1 RESI\n2 DATE 1 JAN 1950\n2 PLAC London", $individual, 'resi1');
        $resi2 = new Fact("1 RESI\n2 DATE 1 JAN 1950\n2 PLAC Paris", $individual, 'resi2');

        $sorted = $this->fact_sort_service->sort(new Collection([$resi1, $resi2]));

        self::assertSame(['resi1', 'resi2'], $this->ids($sorted));
    }

    // =========================================================================
    // Individual facts only — all undated
    // =========================================================================

    public function testUndatedIndividualFactsSortByType(): void
    {
        $individual = $this->stubIndividual();

        $note = new Fact('1 NOTE A note', $individual, 'note');
        $occu = new Fact('1 OCCU Farmer', $individual, 'occu');
        $birt = new Fact('1 BIRT', $individual, 'birt');

        $sorted = $this->fact_sort_service->sort(new Collection([$note, $occu, $birt]));

        // BIRT < OCCU < NOTE in FACT_ORDER
        self::assertSame(['birt', 'occu', 'note'], $this->ids($sorted));
    }

    public function testUndatedSameTypePreservesOrder(): void
    {
        $individual = $this->stubIndividual();

        $occu1 = new Fact('1 OCCU Farmer', $individual, 'occu1');
        $occu2 = new Fact('1 OCCU Baker', $individual, 'occu2');

        $sorted = $this->fact_sort_service->sort(new Collection([$occu1, $occu2]));

        self::assertSame(['occu1', 'occu2'], $this->ids($sorted));
    }

    // =========================================================================
    // Individual facts — mix of dated and undated
    // =========================================================================

    public function testDatedAndUndatedMergeByType(): void
    {
        $individual = $this->stubIndividual();

        $birth = new Fact("1 BIRT\n2 DATE 1 JAN 1950", $individual, 'birth');
        $occu  = new Fact('1 OCCU Farmer', $individual, 'occu');
        $death = new Fact("1 DEAT\n2 DATE 1 JAN 2000", $individual, 'death');

        $sorted = $this->fact_sort_service->sort(new Collection([$occu, $death, $birth]));

        // BIRT(dated) < OCCU(undated, between BIRT and DEAT in type order) < DEAT(dated)
        self::assertSame(['birth', 'occu', 'death'], $this->ids($sorted));
    }

    public function testUndatedFactAfterDeathSortsAfterDeath(): void
    {
        $individual = $this->stubIndividual();

        $birth = new Fact("1 BIRT\n2 DATE 1 JAN 1950", $individual, 'birth');
        $buri  = new Fact('1 BURI', $individual, 'buri');
        $death = new Fact("1 DEAT\n2 DATE 1 JAN 2000", $individual, 'death');

        $sorted = $this->fact_sort_service->sort(new Collection([$buri, $death, $birth]));

        // BIRT(dated) < DEAT(dated) < BURI(undated, after DEAT in type order)
        self::assertSame(['birth', 'death', 'buri'], $this->ids($sorted));
    }

    // =========================================================================
    // Single family — dated events
    // =========================================================================

    public function testSingleFamilyDatedEventsSort(): void
    {
        $family = $this->stubFamily('F1');

        $div  = new Fact("1 DIV\n2 DATE 1 JAN 1990", $family, 'div');
        $marr = new Fact("1 MARR\n2 DATE 1 JAN 1980", $family, 'marr');

        $sorted = $this->fact_sort_service->sort(new Collection([$div, $marr]));

        self::assertSame(['marr', 'div'], $this->ids($sorted));
    }

    // =========================================================================
    // Single family — undated events
    // =========================================================================

    public function testSingleFamilyUndatedEventsSortByType(): void
    {
        $family = $this->stubFamily('F1');

        $div  = new Fact('1 DIV', $family, 'div');
        $marr = new Fact('1 MARR', $family, 'marr');

        $sorted = $this->fact_sort_service->sort(new Collection([$div, $marr]));

        // MARR < DIV in FACT_ORDER
        self::assertSame(['marr', 'div'], $this->ids($sorted));
    }

    // =========================================================================
    // Two families — dated events only
    // =========================================================================

    public function testTwoFamiliesDatedEventsGroupByFamily(): void
    {
        $familyA = $this->stubFamily('FA');
        $familyB = $this->stubFamily('FB');

        $marrA = new Fact("1 MARR\n2 DATE 1 JAN 1980", $familyA, 'marrA');
        $divA  = new Fact("1 DIV\n2 DATE 1 JAN 1985", $familyA, 'divA');
        $marrB = new Fact("1 MARR\n2 DATE 1 JAN 1990", $familyB, 'marrB');
        $divB  = new Fact("1 DIV\n2 DATE 1 JAN 1995", $familyB, 'divB');

        $sorted = $this->fact_sort_service->sort(new Collection([$marrA, $divA, $marrB, $divB]));

        // Each family's events should stay grouped and in chronological order
        self::assertSame(['marrA', 'divA', 'marrB', 'divB'], $this->ids($sorted));
    }

    public function testTwoFamiliesChronologicallyInterleavedSortByDate(): void
    {
        $familyA = $this->stubFamily('FA');
        $familyB = $this->stubFamily('FB');

        // Family B marriage is between family A's marriage and divorce
        $marrA = new Fact("1 MARR\n2 DATE 1 JAN 1980", $familyA, 'marrA');
        $marrB = new Fact("1 MARR\n2 DATE 1 JAN 1982", $familyB, 'marrB');
        $divA  = new Fact("1 DIV\n2 DATE 1 JAN 1985", $familyA, 'divA');
        $divB  = new Fact("1 DIV\n2 DATE 1 JAN 1990", $familyB, 'divB');

        // Input order: family A facts first, then family B
        $sorted = $this->fact_sort_service->sort(new Collection([$marrA, $divA, $marrB, $divB]));

        // Current behavior: dated facts sort chronologically, families are NOT grouped.
        // This may be undesirable — ideally family A events would stay together.
        self::assertSame(['marrA', 'marrB', 'divA', 'divB'], $this->ids($sorted));
    }

    // =========================================================================
    // Two families — undated events only
    // =========================================================================

    public function testTwoFamiliesUndatedEventsGroupByFamily(): void
    {
        $familyA = $this->stubFamily('FA');
        $familyB = $this->stubFamily('FB');

        $marrA = new Fact('1 MARR', $familyA, 'marrA');
        $divA  = new Fact('1 DIV', $familyA, 'divA');
        $marrB = new Fact('1 MARR', $familyB, 'marrB');
        $divB  = new Fact('1 DIV', $familyB, 'divB');

        $sorted = $this->fact_sort_service->sort(new Collection([$marrA, $divA, $marrB, $divB]));

        // Family grouping should be preserved, type order within each family
        self::assertSame(['marrA', 'divA', 'marrB', 'divB'], $this->ids($sorted));
    }

    // =========================================================================
    // Two families — mix of dated and undated
    // =========================================================================

    public function testTwoFamiliesDatedMarriageUndatedDivorce(): void
    {
        $familyA = $this->stubFamily('FA');
        $familyB = $this->stubFamily('FB');

        $marrA = new Fact("1 MARR\n2 DATE 1 JAN 1980", $familyA, 'marrA');
        $divA  = new Fact('1 DIV', $familyA, 'divA');
        $marrB = new Fact("1 MARR\n2 DATE 1 JAN 1990", $familyB, 'marrB');
        $divB  = new Fact('1 DIV', $familyB, 'divB');

        $sorted = $this->fact_sort_service->sort(new Collection([$marrA, $divA, $marrB, $divB]));

        // Each family's events should stay grouped: dated marriage, then undated divorce
        self::assertSame(['marrA', 'divA', 'marrB', 'divB'], $this->ids($sorted));
    }

    public function testTwoFamiliesUndatedMarriageDatedDivorce(): void
    {
        $familyA = $this->stubFamily('FA');
        $familyB = $this->stubFamily('FB');

        $marrA = new Fact('1 MARR', $familyA, 'marrA');
        $divA  = new Fact("1 DIV\n2 DATE 1 JAN 1985", $familyA, 'divA');
        $marrB = new Fact('1 MARR', $familyB, 'marrB');
        $divB  = new Fact("1 DIV\n2 DATE 1 JAN 1995", $familyB, 'divB');

        $sorted = $this->fact_sort_service->sort(new Collection([$marrA, $divA, $marrB, $divB]));

        // Each family's events should stay grouped: undated marriage before dated divorce
        self::assertSame(['marrA', 'divA', 'marrB', 'divB'], $this->ids($sorted));
    }

    // =========================================================================
    // Mixed individual and family facts
    // =========================================================================

    public function testIndividualAndFamilyFactsMergeChronologically(): void
    {
        $individual = $this->stubIndividual();
        $family     = $this->stubFamily('F1');

        $birth = new Fact("1 BIRT\n2 DATE 1 JAN 1950", $individual, 'birth');
        $marr  = new Fact("1 MARR\n2 DATE 1 JAN 1980", $family, 'marr');
        $death = new Fact("1 DEAT\n2 DATE 1 JAN 2000", $individual, 'death');

        $sorted = $this->fact_sort_service->sort(new Collection([$death, $marr, $birth]));

        self::assertSame(['birth', 'marr', 'death'], $this->ids($sorted));
    }

    public function testIndividualAndTwoFamiliesChronological(): void
    {
        $individual = $this->stubIndividual();
        $familyA    = $this->stubFamily('FA');
        $familyB    = $this->stubFamily('FB');

        $birth = new Fact("1 BIRT\n2 DATE 1 JAN 1950", $individual, 'birth');
        $marrA = new Fact("1 MARR\n2 DATE 1 JAN 1980", $familyA, 'marrA');
        $divA  = new Fact("1 DIV\n2 DATE 1 JAN 1985", $familyA, 'divA');
        $marrB = new Fact("1 MARR\n2 DATE 1 JAN 1990", $familyB, 'marrB');
        $death = new Fact("1 DEAT\n2 DATE 1 JAN 2000", $individual, 'death');

        $sorted = $this->fact_sort_service->sort(new Collection([$birth, $marrA, $divA, $marrB, $death]));

        self::assertSame(['birth', 'marrA', 'divA', 'marrB', 'death'], $this->ids($sorted));
    }

    public function testIndividualUndatedWithFamilyDated(): void
    {
        $individual = $this->stubIndividual();
        $family     = $this->stubFamily('F1');

        $birth = new Fact("1 BIRT\n2 DATE 1 JAN 1950", $individual, 'birth');
        $occu  = new Fact('1 OCCU Farmer', $individual, 'occu');
        $marr  = new Fact("1 MARR\n2 DATE 1 JAN 1980", $family, 'marr');
        $death = new Fact("1 DEAT\n2 DATE 1 JAN 2000", $individual, 'death');

        $sorted = $this->fact_sort_service->sort(new Collection([$birth, $occu, $marr, $death]));

        // MARR(29) < OCCU(38) by type-order; one is undated so type-order applies
        self::assertSame(['birth', 'marr', 'occu', 'death'], $this->ids($sorted));
    }

    // =========================================================================
    // Two families — all permutations of dated/undated MARR and DIV
    // =========================================================================

    /**
     * @return array<string,array{array<array{string,string,string,bool}>,array<string>}>
     */
    public static function twoFamilyPermutationsProvider(): array
    {
        // Each entry: [facts-config, expected-ids]
        // Facts config: [id, tag, family(A/B), dated?]
        // Dates chosen so that: marrA(1980) < divA(1985) < marrB(1990) < divB(1995)
        return [
            'all dated' => [
                [
                    ['marrA', 'MARR', 'A', true],
                    ['divA', 'DIV', 'A', true],
                    ['marrB', 'MARR', 'B', true],
                    ['divB', 'DIV', 'B', true],
                ],
                ['marrA', 'divA', 'marrB', 'divB'],
            ],
            'all undated' => [
                [
                    ['marrA', 'MARR', 'A', false],
                    ['divA', 'DIV', 'A', false],
                    ['marrB', 'MARR', 'B', false],
                    ['divB', 'DIV', 'B', false],
                ],
                ['marrA', 'divA', 'marrB', 'divB'],
            ],
            'marrA undated, rest dated' => [
                [
                    ['marrA', 'MARR', 'A', false],
                    ['divA', 'DIV', 'A', true],
                    ['marrB', 'MARR', 'B', true],
                    ['divB', 'DIV', 'B', true],
                ],
                ['marrA', 'divA', 'marrB', 'divB'],
            ],
            'divA undated, rest dated' => [
                [
                    ['marrA', 'MARR', 'A', true],
                    ['divA', 'DIV', 'A', false],
                    ['marrB', 'MARR', 'B', true],
                    ['divB', 'DIV', 'B', true],
                ],
                ['marrA', 'divA', 'marrB', 'divB'],
            ],
            'marrB undated, rest dated' => [
                [
                    ['marrA', 'MARR', 'A', true],
                    ['divA', 'DIV', 'A', true],
                    ['marrB', 'MARR', 'B', false],
                    ['divB', 'DIV', 'B', true],
                ],
                ['marrA', 'divA', 'marrB', 'divB'],
            ],
            'divB undated, rest dated' => [
                [
                    ['marrA', 'MARR', 'A', true],
                    ['divA', 'DIV', 'A', true],
                    ['marrB', 'MARR', 'B', true],
                    ['divB', 'DIV', 'B', false],
                ],
                ['marrA', 'divA', 'marrB', 'divB'],
            ],
            'both marriages undated' => [
                [
                    ['marrA', 'MARR', 'A', false],
                    ['divA', 'DIV', 'A', true],
                    ['marrB', 'MARR', 'B', false],
                    ['divB', 'DIV', 'B', true],
                ],
                ['marrA', 'divA', 'marrB', 'divB'],
            ],
            'both divorces undated' => [
                [
                    ['marrA', 'MARR', 'A', true],
                    ['divA', 'DIV', 'A', false],
                    ['marrB', 'MARR', 'B', true],
                    ['divB', 'DIV', 'B', false],
                ],
                ['marrA', 'divA', 'marrB', 'divB'],
            ],
            'family A undated, family B dated' => [
                [
                    ['marrA', 'MARR', 'A', false],
                    ['divA', 'DIV', 'A', false],
                    ['marrB', 'MARR', 'B', true],
                    ['divB', 'DIV', 'B', true],
                ],
                ['marrA', 'divA', 'marrB', 'divB'],
            ],
            'family A dated, family B undated' => [
                [
                    ['marrA', 'MARR', 'A', true],
                    ['divA', 'DIV', 'A', true],
                    ['marrB', 'MARR', 'B', false],
                    ['divB', 'DIV', 'B', false],
                ],
                ['marrA', 'divA', 'marrB', 'divB'],
            ],
            'marrA dated, divB dated, rest undated' => [
                [
                    ['marrA', 'MARR', 'A', true],
                    ['divA', 'DIV', 'A', false],
                    ['marrB', 'MARR', 'B', false],
                    ['divB', 'DIV', 'B', true],
                ],
                ['marrA', 'divA', 'marrB', 'divB'],
            ],
            'divA dated, marrB dated, rest undated' => [
                [
                    ['marrA', 'MARR', 'A', false],
                    ['divA', 'DIV', 'A', true],
                    ['marrB', 'MARR', 'B', true],
                    ['divB', 'DIV', 'B', false],
                ],
                ['marrA', 'divA', 'marrB', 'divB'],
            ],
            'marrA undated, divB undated, rest dated' => [
                [
                    ['marrA', 'MARR', 'A', false],
                    ['divA', 'DIV', 'A', true],
                    ['marrB', 'MARR', 'B', true],
                    ['divB', 'DIV', 'B', false],
                ],
                ['marrA', 'divA', 'marrB', 'divB'],
            ],
            'divA undated, marrB undated, rest dated' => [
                [
                    ['marrA', 'MARR', 'A', true],
                    ['divA', 'DIV', 'A', false],
                    ['marrB', 'MARR', 'B', false],
                    ['divB', 'DIV', 'B', true],
                ],
                ['marrA', 'divA', 'marrB', 'divB'],
            ],
        ];
    }

    /**
     * @param array<array{string,string,string,bool}> $facts_config
     * @param array<string>                           $expected_ids
     */
    #[DataProvider('twoFamilyPermutationsProvider')]
    public function testTwoFamilyPermutations(array $facts_config, array $expected_ids): void
    {
        $dates = [
            'MARR' => ['A' => '1 JAN 1980', 'B' => '1 JAN 1990'],
            'DIV'  => ['A' => '1 JAN 1985', 'B' => '1 JAN 1995'],
        ];

        $families = [
            'A' => $this->stubFamily('FA'),
            'B' => $this->stubFamily('FB'),
        ];

        $facts = [];

        foreach ($facts_config as [$id, $tag, $family_key, $dated]) {
            $gedcom = $dated
                ? "1 {$tag}\n2 DATE " . $dates[$tag][$family_key]
                : "1 {$tag}";

            $facts[] = new Fact($gedcom, $families[$family_key], $id);
        }

        $sorted = $this->fact_sort_service->sort(new Collection($facts));

        self::assertSame($expected_ids, $this->ids($sorted));
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testIssue3841UndatedOccuSortsAfterBirthWhenCloseRelativeEventPrecedesBirth(): void
    {
        $individual = $this->stubIndividual();

        $occu           = new Fact('1 OCCU occupation', $individual, 'occu');
        $birth          = new Fact("1 BIRT\n2 DATE 1 JUL 1818", $individual, 'birth');
        $relative_death = new Fact("1 EVEN CLOSE_RELATIVE\n2 TYPE Death of father\n2 DATE 1 FEB 1818", $individual, 'relative_death');

        $sorted = $this->fact_sort_service->sort(new Collection([$occu, $birth, $relative_death]));

        self::assertSame(['relative_death', 'birth', 'occu'], $this->ids($sorted));
    }

    public function testIssue3841UndatedMarriageSortsAfterDatedBirth(): void
    {
        $individual = $this->stubIndividual();
        $family     = $this->stubFamily('F1');

        $marr           = new Fact('1 MARR', $family, 'marr');
        $birth          = new Fact("1 BIRT\n2 DATE 1 JUL 1818", $individual, 'birth');
        $relative_death = new Fact("1 EVEN CLOSE_RELATIVE\n2 TYPE Death of father\n2 DATE 1 FEB 1818", $individual, 'relative_death');

        $sorted = $this->fact_sort_service->sort(new Collection([$marr, $birth, $relative_death]));

        self::assertSame(['relative_death', 'birth', 'marr'], $this->ids($sorted));
    }

    public function testDatedPostDeathFactsSortChronologically(): void
    {
        $individual = $this->stubIndividual();

        $birth = new Fact("1 BIRT\n2 DATE 1 JAN 1950", $individual, 'birth');
        $death = new Fact("1 DEAT\n2 DATE 1 JAN 2000", $individual, 'death');
        $prob  = new Fact("1 PROB\n2 DATE 1 FEB 2000", $individual, 'prob');
        $buri  = new Fact("1 BURI\n2 DATE 2 JAN 2000", $individual, 'buri');

        $sorted = $this->fact_sort_service->sort(new Collection([$birth, $death, $prob, $buri]));

        // All dated — sort chronologically, type as tiebreaker for same date
        self::assertSame(['birth', 'death', 'buri', 'prob'], $this->ids($sorted));
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function stubIndividual(): Individual
    {
        $individual = self::createStub(Individual::class);
        $individual->method('tag')->willReturn('INDI');

        return $individual;
    }

    private function stubFamily(string $xref): Family
    {
        $family = self::createStub(Family::class);
        $family->method('tag')->willReturn('FAM');
        $family->method('xref')->willReturn($xref);

        return $family;
    }

    /**
     * @param Collection<int,Fact> $facts
     * @return array<string>
     */
    private function ids(Collection $facts): array
    {
        return array_map(static fn (Fact $fact): string => $fact->id(), $facts->all());
    }
}
