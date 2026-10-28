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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\I18N;

use function strtoupper;

/**
 * LANGUAGE_ID := {Size=1:15}
 * The human language in which the data in the transmission is normally read or
 * written. It is used primarily by programs to select language-specific
 * sorting sequences and phonetic name matching algorithms.
 */
class LanguageId extends AbstractElement
{
    /**
     * Convert a value to a canonical form.
     */
    public function canonical(string $value): string
    {
        return strtoupper(parent::canonical($value));
    }

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        $values = [
            ''              => '',
            'AFRIKAANS'     => 'Afrikaans',
            'ALBANIAN'      => 'shqip',
            'AMHARIC'       => 'አማርኛ',
            'ANGLO-SAXON'   => 'nglisc',
            'ARABIC'        => 'العربية',
            'ARMENIAN'      => 'հայերեն',
            'ASSAMESE'      => 'অসমীয়া',
            'BELORUSIAN'    => 'беларуская',
            'BENGALI'       => 'বাংলা',
            'BRAJ'          => 'BRAJ',
            'BULGARIAN'     => 'български',
            'BURMESE'       => 'မြန်မာ',
            'CANTONESE'     => '粵語',
            'CATALAN'       => 'catal',
            'CATALAN_SPN'   => 'catal',
            'CHURCH-SLAVIC' => 'церковнослове́нскїй',
            'CZECH'         => 'čeština',
            'DANISH'        => 'dansk',
            'DOGRI'         => 'डोगरी',
            'DUTCH'         => 'Nederlands',
            'ENGLISH'       => 'English',
            'ESPERANTO'     => 'esperanto',
            'ESTONIAN'      => 'eesti',
            'FAROESE'       => 'froyskt',
            'FINNISH'       => 'suomi',
            'FRENCH'        => 'français',
            'GEORGIAN'      => 'ქართული',
            'GERMAN'        => 'Deutsch',
            'GREEK'         => 'Ελληνικά',
            'GUJARATI'      => 'ગુજરાતી',
            'HAWAIIAN'      => 'ʻŌlelo Hawaiʻi',
            'HEBREW'        => 'עברית',
            'HINDI'         => 'हिन्दी',
            'HUNGARIAN'     => 'magyar',
            'ICELANDIC'     => 'slenska',
            'INDONESIAN'    => 'Indonesia',
            'ITALIAN'       => 'italiano',
            'JAPANESE'      => '日本語',
            'KANNADA'       => 'ಕನ್ನಡ',
            'KHMER'         => 'ខ្មែរ',
            'KONKANI'       => 'कोंकणी',
            'KOREAN'        => '한국어',
            'LAHNDA'        => 'LAHNDA',
            'LAO'           => 'ລາວ',
            'LATVIAN'       => 'latviešu',
            'LITHUANIAN'    => 'lietuvių',
            'MACEDONIAN'    => 'македонски',
            'MAITHILI'      => 'मैथिली',
            'MALAYALAM'     => 'മലയാളം',
            'MANDRIN'       => 'MANDRIN',
            'MANIPURI'      => 'মৈতৈলোন্',
            'MARATHI'       => 'मराठी',
            'MEWARI'        => 'MEWARI',
            'NAVAHO'        => 'Diné bizaad',
            'NEPALI'        => 'नेपाली',
            'NORWEGIAN'     => 'norsk nynorsk',
            'ORIYA'         => 'ଓଡ଼ିଆ',
            'PAHARI'        => 'पहाड़ी',
            'PALI'          => 'PALI',
            'PANJABI'       => 'ਪੰਜਾਬੀ',
            'PERSIAN'       => 'فارسی',
            'POLISH'        => 'polski',
            'PORTUGUESE'    => 'portugus',
            'PRAKRIT'       => 'PRAKRIT',
            'PUSTO'         => 'پښتو',
            'RAJASTHANI'    => 'राजस्थानी',
            'ROMANIAN'      => 'romnă',
            'RUSSIAN'       => 'русский',
            'SANSKRIT'      => 'संस्कृत भाषा',
            'SERB'          => 'српски',
            'SERBO_CROA'    => 'SERBO_CROA',
            'SLOVAK'        => 'slovenčina',
            'SLOVENE'       => 'slovenščina',
            'SPANISH'       => 'espaol',
            'SWEDISH'       => 'svenska',
            'TAGALOG'       => 'Tagalog',
            'TAMIL'         => 'தமிழ்',
            'TELUGU'        => 'తెలుగు',
            'THAI'          => 'ไทย',
            'TIBETAN'       => 'བོད་སྐད་',
            'TURKISH'       => 'Trke',
            'UKRAINIAN'     => 'українська',
            'URDU'          => 'اردو',
            'VIETNAMESE'    => 'Tiếng Việt',
            'WENDIC'        => 'WENDIC',
            'YIDDISH'       => 'ייִדיש',
        ];

        uasort($values, I18N::compare(...));

        return $values;
    }
}
