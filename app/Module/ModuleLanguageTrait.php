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
use Fisharebest\ExtCalendar\GregorianCalendar;
use Fisharebest\Localization\Locale\LocaleEnUs;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Relationship;
use Illuminate\Database\Query\Builder;

use function mb_substr;

/**
 * Trait ModuleLanguageEventsTrait - default implementation of ModuleLanguageInterface.
 */
trait ModuleLanguageTrait
{
    /**
     * Phone-book ordering of letters.
     *
     * @return array<int,string>
     */
    public function alphabet(): array
    {
        return ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    }

    /**
     * Default calendar used by this language.
     *
     * @return CalendarInterface
     */
    public function calendar(): CalendarInterface
    {
        return new GregorianCalendar();
    }

    /**
     * One of: 'DMY', 'MDY', 'YMD'.
     *
     * @return string
     */
    public function dateOrder(): string
    {
        return 'DMY';
    }

    /**
     * Some languages treat certain letter-combinations as equivalent.
     *
     * @return array<string,string>
     */
    public function equivalentLetters(): array
    {
        return [];
    }

    /**
     * Some languages use digraphs and trigraphs.
     *
     * @param string $string
     *
     * @return string
     */
    public function initialLetter(string $string): string
    {
        return mb_substr($string, 0, 1);
    }

    /**
     * @param string  $column
     * @param string  $letter
     * @param Builder $query
     *
     * @return void
     */
    public function initialLetterSQL(string $column, string $letter, Builder $query): void
    {
        $query->where($column, 'LIKE', '\\' . $letter . '%');
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return  $this->locale()->endonym();
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Language') . ' — ' . $this->title() . ' — ' . $this->locale()->languageTag();
    }

    /**
     * @return LocaleInterface
     */
    public function locale(): LocaleInterface
    {
        return new LocaleEnUs();
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [];
    }
}
