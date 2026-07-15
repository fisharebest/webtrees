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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\ExtCalendar\CalendarInterface;
use Fisharebest\Webtrees\Contracts\LanguageInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Relationship;

/**
 * Default implementation of ModuleLanguageInterface.
 */
trait ModuleLanguageTrait
{
    protected LanguageInterface $language;

    /**
     * @return array<int,string>
     */
    public function alphabet(): array
    {
        return $this->language->alphabet();
    }

    public function calendar(): CalendarInterface
    {
        return $this->language->calendar();
    }

    public function dateOrder(): string
    {
        return $this->language->dateOrder();
    }

    public function initialLetter(string $string): string
    {
        return $this->language->initialLetter($string);
    }

    public function language(): LanguageInterface
    {
        return $this->language;
    }

    /**
     * Ignore diacritics on letters - unless the language considers them a different letter.
     */
    public function normalize(string $text): string
    {
        return $this->language->normalize($text);
    }


    public function title(): string
    {
        return $this->language()->endonym();
    }

    public function description(): string
    {
        return I18N::translate('Language') . ' — ' . $this->title() . ' — ' . $this->language()->languageTag();
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return $this->language->relationships();
    }
}
