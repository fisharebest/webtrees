<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\Elements;

use function strtolower;

/**
 * PHONETIC_TYPE := {Size=5:30}
 * [<user defined> | hangul | kana]
 * Indicates the method used in transforming the text to the phonetic variation.
 * <user define> record method used to arrive at the phonetic variation of the name.
 * hangul        Phonetic method for sounding Korean glifs.
 * kana          Hiragana and/or Katakana characters were used in sounding the Kanji
 *               character used by japanese
 */
class PhoneticType extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 30;

    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        $value = parent::canonical($value);
        $lower = strtolower($value);

        if ($lower === 'hangul' || $lower === 'kana') {
            return $lower;
        }

        return $value;
    }
}
