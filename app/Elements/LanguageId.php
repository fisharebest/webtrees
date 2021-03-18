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

use function preg_replace_callback;

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
        return preg_replace_callback('/[A-Za-z]+/', static function (array $match): string {
            return ucwords($match[0]);
        }, strtolower(parent::canonical($value)));
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
            'Afrikaans'     => (new LocaleAf())->endonym(),
            'Albanian'      => (new LocaleSq())->endonym(),
            'Amharic'       => (new LocaleAm())->endonym(),
            'Anglo-Saxon'   => (new LocaleAng())->endonym(),
            'Arabic'        => (new LocaleAr())->endonym(),
            'Armenian'      => (new LocaleHy())->endonym(),
            'Assamese'      => (new LocaleAs())->endonym(),
            'Belorusian'    => (new LocaleBe())->endonym(),
            'Bengali'       => (new LocaleBn())->endonym(),
            //'Braj' => (new LocaleBra())->endonym(),
            'Bulgarian'     => (new LocaleBg())->endonym(),
            'Burmese'       => (new LocaleMy())->endonym(),
            'Cantonese'     => (new LocaleYue())->endonym(),
            'Catalan'       => (new LocaleCaEsValencia())->endonym(),
            'Catalan_Spn'   => (new LocaleCa())->endonym(),
            'Church-Slavic' => (new LocaleCu())->endonym(),
            'Czech'         => (new LocaleCs())->endonym(),
            'Danish'        => (new LocaleDa())->endonym(),
            //'Dogri' => (new LocaleDoi())->endonym(),
            'Dutch'         => (new LocaleNl())->endonym(),
            'English'       => (new LocaleEn())->endonym(),
            'Esperanto'     => (new LocaleEo())->endonym(),
            'Estonian'      => (new LocaleEt())->endonym(),
            'Faroese'       => (new LocaleFo())->endonym(),
            'Finnish'       => (new LocaleFi())->endonym(),
            'French'        => (new LocaleFr())->endonym(),
            'Georgian'      => (new LocaleKa())->endonym(),
            'German'        => (new LocaleDe())->endonym(),
            'Greek'         => (new LocaleEl())->endonym(),
            'Gujarati'      => (new LocaleGu())->endonym(),
            'Hawaiian'      => (new LocaleHaw())->endonym(),
            'Hebrew'        => (new LocaleHe())->endonym(),
            'Hindi'         => (new LocaleHi())->endonym(),
            'Hungarian'     => (new LocaleHu())->endonym(),
            'Icelandic'     => (new LocaleIs())->endonym(),
            'Indonesian'    => (new LocaleId())->endonym(),
            'Italian'       => (new LocaleIt())->endonym(),
            'Japanese'      => (new LocaleJa())->endonym(),
            'Kannada'       => (new LocaleKn())->endonym(),
            'Khmer'         => (new LocaleKm())->endonym(),
            'Konkani'       => (new LocaleKok())->endonym(),
            'Korean'        => (new LocaleKo())->endonym(),
            //'Lahnda' => (new LocaleLah())->endonym(),
            'Lao'           => (new LocaleLo())->endonym(),
            'Latvian'       => (new LocaleLv())->endonym(),
            'Lithuanian'    => (new LocaleLt())->endonym(),
            'Macedonian'    => (new LocaleMk())->endonym(),
            //'Maithili' => (new LocaleMai())->endonym(),
            'Malayalam'     => (new LocaleMl())->endonym(),
            //'Mandrin' => (new LocaleCmn())->endonym(),
            //'Manipuri' => (new LocaleMni())->endonym(),
            'Marathi'       => (new LocaleMr())->endonym(),
            //'Mewari' => (new LocaleMtr())->endonym(),
            //'Navaho' => (new LocaleNv())->endonym(),
            'Nepali'        => (new LocaleNe())->endonym(),
            'Norwegian'     => (new LocaleNn())->endonym(),
            'Oriya'         => (new LocaleOr())->endonym(),
            //'Pahari' => (new LocalePhr())->endonym(),
            //'Pali' => (new LocalePi())->endonym(),
            'Panjabi'       => (new LocalePa())->endonym(),
            'Persian'       => (new LocaleFa())->endonym(),
            'Polish'        => (new LocalePl())->endonym(),
            'Portuguese'    => (new LocalePt())->endonym(),
            //'Prakrit' => (new LocalePra())->endonym(),
            'Pusto'         => (new LocalePs())->endonym(),
            //'Rajasthani' => (new LocaleRaj())->endonym(),
            'Romanian'      => (new LocaleRo())->endonym(),
            'Russian'       => (new LocaleRu())->endonym(),
            //'Sanskrit' => (new LocaleSa())->endonym(),
            'Serb'          => (new LocaleSr())->endonym(),
            //'Serbo_Croa' => (new LocaleHbs())->endonym(),
            'Slovak'        => (new LocaleSk())->endonym(),
            'Slovene'       => (new LocaleSl())->endonym(),
            'Spanish'       => (new LocaleEs())->endonym(),
            'Swedish'       => (new LocaleSv())->endonym(),
            'Tagalog'       => (new LocaleTl())->endonym(),
            'Tamil'         => (new LocaleTa())->endonym(),
            'Telugu'        => (new LocaleTe())->endonym(),
            'Thai'          => (new LocaleTh())->endonym(),
            'Tibetan'       => (new LocaleBo())->endonym(),
            'Turkish'       => (new LocaleTr())->endonym(),
            'Ukrainian'     => (new LocaleUk())->endonym(),
            'Urdu'          => (new LocaleUr())->endonym(),
            'Vietnamese'    => (new LocaleVi())->endonym(),
            //'Wendic' => (new LocaleWen())->endonym(),
            'Yiddish'       => (new LocaleYi())->endonym(),
        ];

        uasort($values, I18N::comparator());

        return $values;
    }
}
