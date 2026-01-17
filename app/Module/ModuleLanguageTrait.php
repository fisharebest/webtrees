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
use Fisharebest\ExtCalendar\GregorianCalendar;
use Fisharebest\Localization\Locale\LocaleEnUs;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Relationship;
use Normalizer;

use function mb_substr;
use function normalizer_normalize;

/**
 * Default implementation of ModuleLanguageInterface.
 */
trait ModuleLanguageTrait
{
    /**
     * @return array<int,string>
     */
    public function alphabet(): array
    {
        return [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
        ];
    }

    public function calendar(): CalendarInterface
    {
        return new GregorianCalendar();
    }

    public function dateOrder(): string
    {
        return 'DMY';
    }

    public function initialLetter(string $string): string
    {
        return mb_substr($string, 0, 1);
    }

    /**
     * Ignore diacritics on letters - unless the language considers them a different letter.
     */
    public function normalize(string $text): string
    {
        // Decompose any combined characters.
        $decomposed = normalizer_normalize($text, Normalizer::FORM_KD);

        if ($decomposed === false) {
            // Invalid UTF8?
            return $text;
        }

        // Keep any diacritic marks that are significant to this language.
        $text = strtr($decomposed, $this->normalizeExceptions());

        // Remove the others.
        return preg_replace('/\p{M}/u', '', $text);
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [];
    }

    public function title(): string
    {
        return  $this->locale()->endonym();
    }

    public function description(): string
    {
        return I18N::translate('Language') . ' — ' . $this->title() . ' — ' . $this->locale()->languageTag();
    }

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
