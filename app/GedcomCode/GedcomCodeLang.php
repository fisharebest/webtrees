<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\GedcomCode;

use Fisharebest\Localization\Locale;

use function array_search;
use function uasort;

/**
 * Class GedcomCodeLang - Functions and logic for GEDCOM "LANG" codes
 */
class GedcomCodeLang
{
    private const LANGUAGE_IDS = [
        'am'          => 'Amharic',
        'ang'         => 'Anglo-Saxon',
        'ar'          => 'Arabic',
        'hy'          => 'Armenian',
        'as'          => 'Assamese',
        'be'          => 'Belorusian',
        'bn'          => 'Bengali',
        'bra'         => 'Braj',
        'bg'          => 'Bulgarian',
        'my'          => 'Burmese',
        'yue'         => 'Cantonese',
        'ca-valencia' => 'Catalan',
        'ca'          => 'Catalan_Spn',
        'cu'          => 'Church-Slavic',
        'cs'          => 'Czech',
        'da'          => 'Danish',
        'doi'         => 'Dogri',
        'nl'          => 'Dutch',
        'en'          => 'English',
        'eo'          => 'Esperanto',
        'et'          => 'Estonian',
        'fo'          => 'Faroese',
        'fi'          => 'Finnish',
        'fr'          => 'French',
        'ka'          => 'Georgian',
        'de'          => 'German',
        'el'          => 'Greek',
        'gu'          => 'Gujarati',
        'haw'         => 'Hawaiian',
        'he'          => 'Hebrew',
        'hi'          => 'Hindi',
        'hu'          => 'Hungarian',
        'is'          => 'Icelandic',
        'id'          => 'Indonesian',
        'it'          => 'Italian',
        'ja'          => 'Japanese',
        'kn'          => 'Kannada',
        'km'          => 'Khmer',
        'kok'         => 'Konkani',
        'ko'          => 'Korean',
        'lah'         => 'Lahnda',
        'lo'          => 'Lao',
        'lv'          => 'Latvian',
        'lt'          => 'Lithuanian',
        'mk'          => 'Macedonian',
        'mai'         => 'Maithili',
        'ml'          => 'Malayalam',
        'cmn'         => 'Mandrin',
        'mni'         => 'Manipuri',
        'mr'          => 'Marathi',
        'mtr'         => 'Mewari',
        'nv'          => 'Navaho',
        'ne'          => 'Nepali',
        'nn'          => 'Norwegian',
        'or'          => 'Oriya',
        'phr'         => 'Pahari',
        'pi'          => 'Pali',
        'pa'          => 'Panjabi',
        'fa'          => 'Persian',
        'pl'          => 'Polish',
        'pt'          => 'Portuguese',
        'pra'         => 'Prakrit',
        'ps'          => 'Pusto',
        'raj'         => 'Rajasthani',
        'ro'          => 'Romanian',
        'ru'          => 'Russian',
        'sa'          => 'Sanskrit',
        'sr'          => 'Serb',
        'hbs'         => 'Serbo_Croa',
        'sk'          => 'Slovak',
        'sl'          => 'Slovene',
        'es'          => 'Spanish',
        'sv'          => 'Swedish',
        'tl'          => 'Tagalog',
        'ta'          => 'Tamil',
        'te'          => 'Telugu',
        'th'          => 'Thai',
        'bo'          => 'Tibetan',
        'tr'          => 'Turkish',
        'uk'          => 'Ukrainian',
        'ur'          => 'Urdu',
        'vi'          => 'Vietnamese',
        'wen'         => 'Wendic',
        'yi'          => 'Yiddish',
    ];

    /**
     * Display value for a language tag.
     *
     * @param string $type
     *
     * @return string
     */
    public static function getValue(string $type): string
    {
        $code = array_search($type, self::LANGUAGE_IDS, true);

        if ($code === false) {
            return $type;
        }

        try {
            return Locale::create($code)->endonym();
        } catch (\DomainException $ex) {
            return $type;
        }
    }

    /**
     * A list of all possible values for LANG fields.
     *
     * @return string[]
     */
    public static function getValues(): array
    {
        $values = [];
        foreach (self::LANGUAGE_IDS as $value) {
            $values[$value] = self::getValue($value);
        }

        uasort($values, '\Fisharebest\Webtrees\I18N::strcasecmp');

        return $values;
    }
}
