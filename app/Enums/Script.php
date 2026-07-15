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

namespace Fisharebest\Webtrees\Enums;

enum Script: string
{
    // The order is important, as we use it for script matching.
    // Japn and Kore should come before Hans/Hant, as they also use Han script.
    // Hans should come before Hant, as most of our users are in mainland China.
    // Latn should come last, as other texts often contain some Latin characters.
    case Arab = 'Arab';
    case Armn = 'Armn';
    case Cyrl = 'Cyrl';
    case Deva = 'Deva';
    case Geor = 'Geor';
    case Grek = 'Grek';
    case Jpan = 'Jpan';
    case Kore = 'Kore';
    case Hans = 'Hans';
    case Hant = 'Hant';
    case Hebr = 'Hebr';
    case Java = 'Java';
    case Sund = 'Sund';
    case Taml = 'Taml';
    case Thaa = 'Thaa';
    case Thai = 'Thai';
    case Latn = 'Latn';

    public static function fromText(string $text): self
    {
        foreach (self::cases() as $script) {
            if (preg_match($script->regex(), $text) === 1) {
                return $script;
            }
        }

        return self::Latn;
    }

    private function regex(): string
    {
        return match ($this) {
            self::Arab => '/\\p{Arabic}/u',
            self::Armn => '/\\p{Armenian}/u',
            self::Cyrl => '/\\p{Cyrillic}/u',
            self::Deva => '/\\p{Devanagari}/u',
            self::Geor => '/\\p{Georgian}/u',
            self::Grek => '/\\p{Greek}/u',
            self::Hans,
            self::Hant => '/\\p{Han}/u',
            self::Hebr => '/\\p{Hebrew}/u',
            self::Java => '/\\p{Javanese}/u',
            self::Jpan => '/[\\p{Hiragana}\\p{Katakana}]/u',
            self::Kore => '/\\p{Hangul}/u',
            self::Latn => '/\\p{Latin}/u',
            self::Sund => '/\\p{Sundanese}/u',
            self::Taml => '/\\p{Tamil}/u',
            self::Thaa => '/\\p{Thaana}/u',
            self::Thai => '/\\p{Thai}/u',
        };
    }

    public function textDirection(): TextDirection
    {
        return match ($this) {
            self::Arab,
            self::Hebr,
            self::Thaa => TextDirection::RTL,
            default => TextDirection::LTR,
        };
    }
}
