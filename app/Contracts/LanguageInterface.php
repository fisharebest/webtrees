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

namespace Fisharebest\Webtrees\Contracts;

use Collator;
use Fisharebest\ExtCalendar\CalendarInterface;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Enums\PluralRule;
use Fisharebest\Webtrees\Enums\Script;
use Fisharebest\Webtrees\Enums\TextDirection;
use Fisharebest\Webtrees\Enums\Weekday;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;

/**
 * Interface LanguageInterface - provide translation and localization.
 */
interface LanguageInterface
{
    /**
     * @return array<int,string>
     */
    public function alphabet(): array;

    public function calendar(): CalendarInterface;

    public function dateOrder(): string;

    public function digits(string|int $string): string;

    public function number(float $number): string;

    public function percentage(float $number): string;

    /**
     * Format a Date object as a localized string.
     */
    public function formatDate(Date $date): string;

    /**
     * Turns ['a', 'b', 'c'] into "a, b, c".
     *
     * @param array<string> $items
     */
    public function formatList(array $items): string;

    /**
     * Turns ['a', 'b', 'c'] into "a, b and c" (or the equivalent in other languages).
     *
     * @param array<string> $items
     */
    public function formatListAnd(array $items): string;

    /**
     * Turns ['a', 'b', 'c'] into "a, b or c" (or the equivalent in other languages).
     *
     * @param array<string> $items
     */
    public function formatListOr(array $items): string;

    /**
     * Extract the initial letter (or letters) of a string.
     */
    public function initialLetter(string $string): string;

    public function endonym(): string;

    public function languageTag(): string;

    public function collator(): Collator|null;

    public function firstDay(): Weekday;

    public function paperSize(): PaperSize;

    public function pluralRule(): PluralRule;

    public function script(): Script;

    public function textDirection(): TextDirection;

    public function strtolower(string $string): string;

    public function strtoupper(string $string): string;

    /**
     * Ignore diacritics on letters - unless the language considers them a different letter.
     */
    public function normalize(string $text): string;

    /**
     * @return array<Relationship>
     */
    public function relationships(): array;
}
