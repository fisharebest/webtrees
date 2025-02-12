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

use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Localization\Locale\LocalePl;
use Fisharebest\Localization\Locale\LocaleUz;
use Fisharebest\Webtrees\Encodings\UTF8;

class LanguageUzbek extends AbstractModule implements ModuleLanguageInterface
{
    use ModuleLanguageTrait;

    /**
     * @return array<int,string>
     */
    public function alphabet(): array
    {
        return [
            'A',
            'B',
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
            'X',
            'Y',
            'Z',
            'O' . UTF8::MODIFIER_LETTER_TURNED_COMMA,
            'G' . UTF8::MODIFIER_LETTER_TURNED_COMMA,
            'SH',
            'CH',
            'NG',
        ];
    }

    public function initialLetter(string $string): string
    {
        if (str_starts_with($string, 'O' . UTF8::MODIFIER_LETTER_TURNED_COMMA)) {
            return 'O' . UTF8::MODIFIER_LETTER_TURNED_COMMA;
        }

        if (str_starts_with($string, 'G' . UTF8::MODIFIER_LETTER_TURNED_COMMA)) {
            return 'G' . UTF8::MODIFIER_LETTER_TURNED_COMMA;
        }

        if (str_starts_with($string, 'SH')) {
            return 'SH';
        }

        if (str_starts_with($string, 'CH')) {
            return 'CH';
        }

        if (str_starts_with($string, 'NG')) {
            return 'NG';
        }

        return mb_substr($string, 0, 1);
    }

    public function isEnabledByDefault(): bool
    {
        return false;
    }

    public function locale(): LocaleInterface
    {
        return new LocaleUz();
    }
}
