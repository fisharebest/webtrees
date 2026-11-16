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

namespace Fisharebest\Webtrees\Tests\Feature\Views;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Enums\Sex;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

use function view;

#[CoversNothing]
class FactDateTest extends TestCase
{
    /**
     * @return iterable<string,array{string}>
     */
    public static function tagsWithoutCalculatedAge(): iterable
    {
        yield 'birth' => ['BIRT'];
        yield 'creation' => ['_CREA'];
        yield 'research task' => ['_TODO'];
        yield 'LDS baptism' => ['BAPL'];
        yield 'LDS confirmation' => ['CONL'];
        yield 'LDS endowment' => ['ENDL'];
        yield 'LDS child sealing' => ['SLGC'];
    }

    #[DataProvider('tagsWithoutCalculatedAge')]
    public function testExcludedIndividualFactsDoNotShowCalculatedAge(string $tag): void
    {
        $individual = $this->individual();
        $fact       = new Fact("1 " . $tag . "\n2 DATE 1 JAN 1930", $individual, '');

        $html = view('fact-date', ['cal_link' => false, 'fact' => $fact, 'record' => $individual, 'time' => false]);

        self::assertStringNotContainsString('<span class="age">', $html);
    }

    public function testFamilyLdsSealingDoesNotShowCalculatedAges(): void
    {
        $husband = $this->individual();
        $family  = self::createStub(Family::class);
        $family->method('tag')->willReturn('FAM');
        $family->method('husband')->willReturn($husband);
        $family->method('tree')->willReturn(self::createStub(Tree::class));
        $fact = new Fact("1 SLGS\n2 DATE 1 JAN 1930\n2 HUSB\n3 AGE 30y", $family, '');

        $html = view('fact-date', ['cal_link' => false, 'fact' => $fact, 'record' => $family, 'time' => false]);

        self::assertStringContainsString('<span class="age">30', $html);
        self::assertStringNotContainsString('(aged ', $html);
    }

    public function testOrdinaryIndividualFactShowsCalculatedAge(): void
    {
        $individual = $this->individual();
        $fact       = new Fact("1 OCCU\n2 DATE 1 JAN 1930", $individual, '');

        $html = view('fact-date', ['cal_link' => false, 'fact' => $fact, 'record' => $individual, 'time' => false]);

        self::assertStringContainsString('(aged 30', $html);
    }

    public function testPosthumousLdsFactDoesNotShowTimeAfterDeath(): void
    {
        $individual = $this->individual(true);
        $fact = new Fact("1 SLGC\n2 DATE 1 JAN 1960", $individual, '');

        $html = view('fact-date', ['cal_link' => false, 'fact' => $fact, 'record' => $individual, 'time' => false]);

        self::assertStringNotContainsString('after death', $html);
        self::assertStringNotContainsString('<span class="age">', $html);
    }

    public function testRecordedAgeRemainsVisibleForExcludedFact(): void
    {
        $individual = $this->individual();
        $fact       = new Fact("1 BAPL\n2 DATE 1 JAN 1930\n2 AGE 30y", $individual, '');

        $html = view('fact-date', ['cal_link' => false, 'fact' => $fact, 'record' => $individual, 'time' => false]);

        self::assertStringContainsString('<span class="age">30', $html);
    }

    private function individual(bool $has_death_event = false): Individual
    {
        $individual = self::createStub(Individual::class);
        $individual->method('tag')->willReturn('INDI');
        $individual->method('tree')->willReturn(self::createStub(Tree::class));
        $individual->method('getBirthDate')->willReturn(new Date('1 JAN 1900'));
        $death_events = $has_death_event ? [new Fact("1 DEAT\n2 DATE 1 JAN 1950", $individual, '')] : [];
        $individual->method('facts')->willReturn(new Collection($death_events));
        $individual->method('sex')->willReturn(Sex::Male);
        $individual->method('isDead')->willReturn($has_death_event);

        return $individual;
    }
}
