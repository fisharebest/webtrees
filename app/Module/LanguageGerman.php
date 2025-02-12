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

use Fisharebest\Localization\Locale\LocaleDe;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\Encodings\UTF8;
use Illuminate\Database\Query\Builder;

class LanguageGerman extends AbstractModule implements ModuleLanguageInterface
{
    use ModuleLanguageTrait;

    public function locale(): LocaleInterface
    {
        return new LocaleDe();
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_DIAERESIS    => 'AE',
            'O' . UTF8::COMBINING_DIAERESIS    => 'OE',
            'U' . UTF8::COMBINING_DIAERESIS    => 'UE',
            UTF8::LATIN_CAPITAL_LETTER_SHARP_S => 'SS',
            'a' . UTF8::COMBINING_DIAERESIS    => 'ae',
            'o' . UTF8::COMBINING_DIAERESIS    => 'oe',
            'u' . UTF8::COMBINING_DIAERESIS    => 'ue',
            UTF8::LATIN_SMALL_LETTER_SHARP_S   => 'ss',
        ];
    }
}
