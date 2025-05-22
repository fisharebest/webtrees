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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Localization\Locale\LocaleAf;
use Fisharebest\Localization\Locale\LocaleAm;
use Fisharebest\Localization\Locale\LocaleAng;
use Fisharebest\Localization\Locale\LocaleAr;
use Fisharebest\Localization\Locale\LocaleAs;
use Fisharebest\Localization\Locale\LocaleBe;
use Fisharebest\Localization\Locale\LocaleBg;
use Fisharebest\Localization\Locale\LocaleBn;
use Fisharebest\Localization\Locale\LocaleBo;
use Fisharebest\Localization\Locale\LocaleCa;
use Fisharebest\Localization\Locale\LocaleCaEsValencia;
use Fisharebest\Localization\Locale\LocaleCs;
use Fisharebest\Localization\Locale\LocaleCu;
use Fisharebest\Localization\Locale\LocaleDa;
use Fisharebest\Localization\Locale\LocaleDe;
use Fisharebest\Localization\Locale\LocaleEl;
use Fisharebest\Localization\Locale\LocaleEn;
use Fisharebest\Localization\Locale\LocaleEo;
use Fisharebest\Localization\Locale\LocaleEs;
use Fisharebest\Localization\Locale\LocaleEt;
use Fisharebest\Localization\Locale\LocaleFa;
use Fisharebest\Localization\Locale\LocaleFi;
use Fisharebest\Localization\Locale\LocaleFo;
use Fisharebest\Localization\Locale\LocaleFr;
use Fisharebest\Localization\Locale\LocaleGu;
use Fisharebest\Localization\Locale\LocaleHaw;
use Fisharebest\Localization\Locale\LocaleHe;
use Fisharebest\Localization\Locale\LocaleHi;
use Fisharebest\Localization\Locale\LocaleHu;
use Fisharebest\Localization\Locale\LocaleHy;
use Fisharebest\Localization\Locale\LocaleId;
use Fisharebest\Localization\Locale\LocaleIs;
use Fisharebest\Localization\Locale\LocaleIt;
use Fisharebest\Localization\Locale\LocaleJa;
use Fisharebest\Localization\Locale\LocaleKa;
use Fisharebest\Localization\Locale\LocaleKm;
use Fisharebest\Localization\Locale\LocaleKn;
use Fisharebest\Localization\Locale\LocaleKo;
use Fisharebest\Localization\Locale\LocaleKok;
use Fisharebest\Localization\Locale\LocaleLo;
use Fisharebest\Localization\Locale\LocaleLt;
use Fisharebest\Localization\Locale\LocaleLv;
use Fisharebest\Localization\Locale\LocaleMk;
use Fisharebest\Localization\Locale\LocaleMl;
use Fisharebest\Localization\Locale\LocaleMr;
use Fisharebest\Localization\Locale\LocaleMy;
use Fisharebest\Localization\Locale\LocaleNe;
use Fisharebest\Localization\Locale\LocaleNl;
use Fisharebest\Localization\Locale\LocaleNn;
use Fisharebest\Localization\Locale\LocaleOr;
use Fisharebest\Localization\Locale\LocalePa;
use Fisharebest\Localization\Locale\LocalePl;
use Fisharebest\Localization\Locale\LocalePs;
use Fisharebest\Localization\Locale\LocalePt;
use Fisharebest\Localization\Locale\LocaleRo;
use Fisharebest\Localization\Locale\LocaleRu;
use Fisharebest\Localization\Locale\LocaleSk;
use Fisharebest\Localization\Locale\LocaleSl;
use Fisharebest\Localization\Locale\LocaleSq;
use Fisharebest\Localization\Locale\LocaleSr;
use Fisharebest\Localization\Locale\LocaleSv;
use Fisharebest\Localization\Locale\LocaleTa;
use Fisharebest\Localization\Locale\LocaleTe;
use Fisharebest\Localization\Locale\LocaleTh;
use Fisharebest\Localization\Locale\LocaleTl;
use Fisharebest\Localization\Locale\LocaleTr;
use Fisharebest\Localization\Locale\LocaleUk;
use Fisharebest\Localization\Locale\LocaleUr;
use Fisharebest\Localization\Locale\LocaleVi;
use Fisharebest\Localization\Locale\LocaleYi;
use Fisharebest\Localization\Locale\LocaleYue;
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
     *
     * @param string $value
     *
     * @return string
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
            'AFRIKAANS'     => (new LocaleAf())->endonym(),
            'ALBANIAN'      => (new LocaleSq())->endonym(),
            'AMHARIC'       => (new LocaleAm())->endonym(),
            'ANGLO-SAXON'   => (new LocaleAng())->endonym(),
            'ARABIC'        => (new LocaleAr())->endonym(),
            'ARMENIAN'      => (new LocaleHy())->endonym(),
            'ASSAMESE'      => (new LocaleAs())->endonym(),
            'BELORUSIAN'    => (new LocaleBe())->endonym(),
            'BENGALI'       => (new LocaleBn())->endonym(),
            //'BRAJ' => (new LocaleBra())->endonym(),
            'BULGARIAN'     => (new LocaleBg())->endonym(),
            'BURMESE'       => (new LocaleMy())->endonym(),
            'CANTONESE'     => (new LocaleYue())->endonym(),
            'CATALAN'       => (new LocaleCaEsValencia())->endonym(),
            'CATALAN_SPN'   => (new LocaleCa())->endonym(),
            'CHURCH-SLAVIC' => (new LocaleCu())->endonym(),
            'CZECH'         => (new LocaleCs())->endonym(),
            'DANISH'        => (new LocaleDa())->endonym(),
            //'DOGRI' => (new LocaleDoi())->endonym(),
            'DUTCH'         => (new LocaleNl())->endonym(),
            'ENGLISH'       => (new LocaleEn())->endonym(),
            'ESPERANTO'     => (new LocaleEo())->endonym(),
            'ESTONIAN'      => (new LocaleEt())->endonym(),
            'FAROESE'       => (new LocaleFo())->endonym(),
            'FINNISH'       => (new LocaleFi())->endonym(),
            'FRENCH'        => (new LocaleFr())->endonym(),
            'GEORGIAN'      => (new LocaleKa())->endonym(),
            'GERMAN'        => (new LocaleDe())->endonym(),
            'GREEK'         => (new LocaleEl())->endonym(),
            'GUJARATI'      => (new LocaleGu())->endonym(),
            'HAWAIIAN'      => (new LocaleHaw())->endonym(),
            'HEBREW'        => (new LocaleHe())->endonym(),
            'HINDI'         => (new LocaleHi())->endonym(),
            'HUNGARIAN'     => (new LocaleHu())->endonym(),
            'ICELANDIC'     => (new LocaleIs())->endonym(),
            'INDONESIAN'    => (new LocaleId())->endonym(),
            'ITALIAN'       => (new LocaleIt())->endonym(),
            'JAPANESE'      => (new LocaleJa())->endonym(),
            'KANNADA'       => (new LocaleKn())->endonym(),
            'KHMER'         => (new LocaleKm())->endonym(),
            'KONKANI'       => (new LocaleKok())->endonym(),
            'KOREAN'        => (new LocaleKo())->endonym(),
            //'LAHNDA' => (new LocaleLah())->endonym(),
            'LAO'           => (new LocaleLo())->endonym(),
            'LATVIAN'       => (new LocaleLv())->endonym(),
            'LITHUANIAN'    => (new LocaleLt())->endonym(),
            'MACEDONIAN'    => (new LocaleMk())->endonym(),
            //'MAITHILI' => (new LocaleMai())->endonym(),
            'MALAYALAM'     => (new LocaleMl())->endonym(),
            //'MANDRIN' => (new LocaleCmn())->endonym(),
            //'MANIPURI' => (new LocaleMni())->endonym(),
            'MARATHI'       => (new LocaleMr())->endonym(),
            //'MEWARI' => (new LocaleMtr())->endonym(),
            //'NAVAHO' => (new LocaleNv())->endonym(),
            'NEPALI'        => (new LocaleNe())->endonym(),
            'NORWEGIAN'     => (new LocaleNn())->endonym(),
            'ORIYA'         => (new LocaleOr())->endonym(),
            //'PAHARI' => (new LocalePhr())->endonym(),
            //'PALI' => (new LocalePi())->endonym(),
            'PANJABI'       => (new LocalePa())->endonym(),
            'PERSIAN'       => (new LocaleFa())->endonym(),
            'POLISH'        => (new LocalePl())->endonym(),
            'PORTUGUESE'    => (new LocalePt())->endonym(),
            //'PRAKRIT' => (new LocalePra())->endonym(),
            'PUSTO'         => (new LocalePs())->endonym(),
            //'RAJASTHANI' => (new LocaleRaj())->endonym(),
            'ROMANIAN'      => (new LocaleRo())->endonym(),
            'RUSSIAN'       => (new LocaleRu())->endonym(),
            //'SANSKRIT' => (new LocaleSa())->endonym(),
            'SERB'          => (new LocaleSr())->endonym(),
            //'SERBO_CROA' => (new LocaleHbs())->endonym(),
            'SLOVAK'        => (new LocaleSk())->endonym(),
            'SLOVENE'       => (new LocaleSl())->endonym(),
            'SPANISH'       => (new LocaleEs())->endonym(),
            'SWEDISH'       => (new LocaleSv())->endonym(),
            'TAGALOG'       => (new LocaleTl())->endonym(),
            'TAMIL'         => (new LocaleTa())->endonym(),
            'TELUGU'        => (new LocaleTe())->endonym(),
            'THAI'          => (new LocaleTh())->endonym(),
            'TIBETAN'       => (new LocaleBo())->endonym(),
            'TURKISH'       => (new LocaleTr())->endonym(),
            'UKRAINIAN'     => (new LocaleUk())->endonym(),
            'URDU'          => (new LocaleUr())->endonym(),
            'VIETNAMESE'    => (new LocaleVi())->endonym(),
            //'WENDIC' => (new LocaleWen())->endonym(),
            'YIDDISH'       => (new LocaleYi())->endonym(),
        ];

        uasort($values, I18N::comparator());

        return $values;
    }
}
