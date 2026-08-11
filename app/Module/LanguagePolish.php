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

use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\I18N\Languages\Polish;

class LanguagePolish extends AbstractModule implements ModuleLanguageInterface
{
    use ModuleLanguageTrait;

    public function __construct()
    {
        $this->language = new Polish();
    }
    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'C' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_CAPITAL_LETTER_C_WITH_ACUTE,
            'L' . UTF8::COMBINING_SHORT_STROKE_OVERLAY => UTF8::LATIN_CAPITAL_LETTER_L_WITH_STROKE,
            'O' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_CAPITAL_LETTER_O_WITH_ACUTE,
            'S' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_CAPITAL_LETTER_S_WITH_ACUTE,
            'Z' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_ACUTE,
            'Z' . UTF8::COMBINING_DOT_ABOVE            => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_DOT_ABOVE,
            'c' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_SMALL_LETTER_C_WITH_ACUTE,
            'l' . UTF8::COMBINING_SHORT_STROKE_OVERLAY => UTF8::LATIN_SMALL_LETTER_L_WITH_STROKE,
            'o' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_SMALL_LETTER_O_WITH_ACUTE,
            's' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_SMALL_LETTER_S_WITH_ACUTE,
            'z' . UTF8::COMBINING_ACUTE_ACCENT         => UTF8::LATIN_SMALL_LETTER_Z_WITH_ACUTE,
            'z' . UTF8::COMBINING_DOT_ABOVE            => UTF8::LATIN_SMALL_LETTER_Z_WITH_DOT_ABOVE,
        ];
    }
}
