<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

/**
 * Interface ModuleLanguageInterface - provide translation and localization.
 */
interface ModuleLanguageInterface extends ModuleInterface
{
    /**
     * @return array<int,string>
     */
    public function alphabet(): array;

    public function calendar(): CalendarInterface;

    public function dateOrder(): string;

    public function initialLetter(string $string): string;

    public function locale(): LocaleInterface;

    /**
     * Ignore diacritics on letters - unless the language considers them a different letter.
     *
     * @param string $text
     *
     * @return string
     */
    public function normalize(string $text): string;

    /**
     * @return array<Relationship>
     */
    public function relationships(): array;
}
