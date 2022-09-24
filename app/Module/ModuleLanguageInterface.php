<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\ExtCalendar\CalendarInterface;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\Relationship;
use Illuminate\Database\Query\Builder;

/**
 * Interface ModuleLanguageInterface - provide translation and localization.
 */
interface ModuleLanguageInterface extends ModuleInterface
{
    /**
     * Phone-book ordering of letters.
     *
     * @return array<int,string>
     */
    public function alphabet(): array;

    /**
     * Default calendar used by this language.
     *
     * @return CalendarInterface
     */
    public function calendar(): CalendarInterface;

    /**
     * One of: 'DMY', 'MDY', 'YMD'.
     *
     * @return string
     */
    public function dateOrder(): string;

    /**
     * Some languages treat certain letter-combinations as equivalent.
     *
     * @return array<string,string>
     */
    public function equivalentLetters(): array;


    /**
     * Some languages use digraphs and trigraphs.
     *
     * @param string $string
     *
     * @return string
     */
    public function initialLetter(string $string): string;

    /**
     * @param string  $column
     * @param string  $letter
     * @param Builder $query
     *
     * @return void
     */
    public function initialLetterSQL(string $column, string $letter, Builder $query): void;

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface;

    /**
     * @return array<Relationship>
     */
    public function relationships(): array;
}
