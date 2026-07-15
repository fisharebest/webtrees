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

namespace Fisharebest\Webtrees\I18N\Languages;

use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

final readonly class Lithuanian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::ThreeFormsLithuanian;

    protected const string    ENDONYM            = 'lietuvių';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'lt';
    protected const string    LOCALE_CODE        = 'lt_LT@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    NEGATIVE_SYMBOL    = UTF8::MINUS_SIGN;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'apie %s';
    protected const string    DATE_AFTER         = 'po %s';
    protected const string    DATE_BEFORE        = 'prieš %s';
    protected const string    DATE_BETWEEN_AND   = 'tarp %s ir %s';
    protected const string    DATE_CALCULATED    = 'apskaičiuota %s';
    protected const string    DATE_ESTIMATED     = 'liko %s';
    protected const string    DATE_FROM          = 'iš %s';
    protected const string    DATE_FROM_TO       = 'nuo %s iki %s';
    protected const string    DATE_INTERPRETED   = 'nutraukta %s';
    protected const string    DATE_TO            = 'į %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'm.' . UTF8::NO_BREAK_SPACE . 'Prieš' . UTF8::NO_BREAK_SPACE . 'Kristų';
    protected const string    LIST_SEPARATOR_AND = ' ir ';
    protected const string    LIST_SEPARATOR_OR  = ' arba ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'sausis',
        'vasaris',
        'kovas',
        'balandis',
        'gegužė',
        'birželis',
        'liepa',
        'rugpjūtis',
        'rugsėjis',
        'spalis',
        'lapkritis',
        'gruodis',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

    protected const array GREGORIAN_MONTHS_GENITIVE = [
        '',
        'sausio',
        'vasario',
        'kovo',
        'balandžio',
        'gegužės',
        'birželio',
        'liepos',
        'rugpjūčio',
        'rugsėjo',
        'spalio',
        'lapkričio',
        'gruodžio',
    ];

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tishrei',
        'Heshvan',
        'Kislev',
        'Tevet',
        'Shevat',
        'Adar I',
        'Adar II',
        'Adar',
        'Nissan',
        'Iyar',
        'Sivan',
        'Tamuz',
        'Av',
        'Elul',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'Vendémiaire',
        'Brumaire',
        'Frimaire',
        'Nivôse',
        'Pluviôse',
        'Ventôse',
        'Germinal',
        'Floréal',
        'Prairial',
        'Messidor',
        'Thermidor',
        'Fructidor',
        'jours complémentaires',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Muharram',
        'Safar',
        'Rabi’ al-awwal',
        'Rabi’ al-thani',
        'Jumada al-awwal',
        'Jumada al-thani',
        'Rajab',
        'Sha’aban',
        'Ramadan',
        'Shawwal',
        'Dhu al-Qi’dah',
        'Dhu al-Hijjah',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Farvardin',
        'Ordibehesht',
        'Khordad',
        'Tir',
        'Mordad',
        'Shahrivar',
        'Mehr',
        'Aban',
        'Azar',
        'Dey',
        'Bahman',
        'Esfand',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;
    /**
     * @return array<int,string>
     */
    /** @var array<int,string> */
    protected const array ALPHABET = [
        'A',
        UTF8::LATIN_CAPITAL_LETTER_A_WITH_OGONEK,
        'B',
        'C',
        UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
        'D',
        'E',
        UTF8::LATIN_CAPITAL_LETTER_E_WITH_OGONEK,
        UTF8::LATIN_CAPITAL_LETTER_E_WITH_DOT_ABOVE,
        'F',
        'G',
        'H',
        'I',
        'Y',
        UTF8::LATIN_CAPITAL_LETTER_I_WITH_OGONEK,
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'R',
        'S',
        UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
        'T',
        'U',
        UTF8::LATIN_CAPITAL_LETTER_U_WITH_OGONEK,
        UTF8::LATIN_CAPITAL_LETTER_U_WITH_MACRON,
        'V',
        'Z',
        UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
    ];

    protected function assembleDate(string $day, string $month, string $year): string
    {
        return parent::assembleDate($year, $month, $day);
    }


    public function dateOrder(): string
    {
        return 'YMD';
    }

    /**
     * Gregorian/Julian month names — case-inflected.
     *
     * @return array<int,string>
     */
    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_A_WITH_OGONEK,
            'C' . UTF8::COMBINING_CARON     => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
            'E' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_E_WITH_OGONEK,
            'E' . UTF8::COMBINING_DOT_ABOVE => UTF8::LATIN_CAPITAL_LETTER_E_WITH_DOT_ABOVE,
            'I' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_I_WITH_OGONEK,
            'S' . UTF8::COMBINING_CARON     => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
            'U' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_U_WITH_OGONEK,
            'U' . UTF8::COMBINING_MACRON    => UTF8::LATIN_CAPITAL_LETTER_U_WITH_MACRON,
            'Z' . UTF8::COMBINING_CARON     => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
            'a' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_A_WITH_OGONEK,
            'c' . UTF8::COMBINING_CARON     => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
            'e' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_E_WITH_OGONEK,
            'e' . UTF8::COMBINING_DOT_ABOVE => UTF8::LATIN_CAPITAL_LETTER_E_WITH_DOT_ABOVE,
            'i' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_I_WITH_OGONEK,
            's' . UTF8::COMBINING_CARON     => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
            'u' . UTF8::COMBINING_OGONEK    => UTF8::LATIN_CAPITAL_LETTER_U_WITH_OGONEK,
            'u' . UTF8::COMBINING_MACRON    => UTF8::LATIN_CAPITAL_LETTER_U_WITH_MACRON,
            'z' . UTF8::COMBINING_CARON     => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Lithuanian genitive: nominative + genitive form
        $rel = static fn (string $nom, string $gen): array => [$nom, '%s ' . $gen];

        // Dynamic prefix for great- generations: pro- repeats
        $pro = static fn (int $n, string $nom, string $gen): array => [
            str_repeat('pro', $n) . $nom,
            '%s ' . str_repeat('pro', $n) . $gen,
        ];

        return [
            // Adopted
            Relationship::fixed(...$rel('įdukra', 'įdukros'))->adopted()->daughter(),
            Relationship::fixed(...$rel('įsūnis', 'įsūnio'))->adopted()->son(),
            Relationship::fixed(...$rel('įvaikis', 'įvaikio'))->adopted()->child(),
            Relationship::fixed(...$rel('įmotė', 'įmotės'))->adoptive()->mother(),
            Relationship::fixed(...$rel('įtėvis', 'įtėvio'))->adoptive()->father(),
            Relationship::fixed(...$rel('įtėvis/įmotė', 'įtėvio/įmotės'))->adoptive()->parent(),
            // Foster
            Relationship::fixed(...$rel('globotinė', 'globotinės'))->fostered()->daughter(),
            Relationship::fixed(...$rel('globotinis', 'globotinio'))->fostered()->son(),
            Relationship::fixed(...$rel('globotinis', 'globotinio'))->fostered()->child(),
            Relationship::fixed(...$rel('globėja', 'globėjos'))->fostering()->mother(),
            Relationship::fixed(...$rel('globėjas', 'globėjo'))->fostering()->father(),
            Relationship::fixed(...$rel('globėjas/globėja', 'globėjo/globėjos'))->fostering()->parent(),
            // Parents
            Relationship::fixed(...$rel('motina', 'motinos'))->mother(),
            Relationship::fixed(...$rel('tėvas', 'tėvo'))->father(),
            Relationship::fixed(...$rel('tėvas/motina', 'tėvo/motinos'))->parent(),
            // Children
            Relationship::fixed(...$rel('dukra', 'dukros'))->daughter(),
            Relationship::fixed(...$rel('sūnus', 'sūnaus'))->son(),
            Relationship::fixed(...$rel('vaikas', 'vaiko'))->child(),
            // Siblings
            Relationship::fixed(...$rel('sesuo', 'sesers'))->sister(),
            Relationship::fixed(...$rel('brolis', 'brolio'))->brother(),
            Relationship::fixed(...$rel('brolis/sesuo', 'brolio/sesers'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$rel('pusseserė', 'pusseserės'))->parent()->daughter(),
            Relationship::fixed(...$rel('pusbrolis', 'pusbrolio'))->parent()->son(),
            // Stepfamily
            Relationship::fixed(...$rel('pamotė', 'pamotės'))->parent()->wife(),
            Relationship::fixed(...$rel('patėvis', 'patėvio'))->parent()->husband(),
            Relationship::fixed(...$rel('podukra', 'podukros'))->married()->spouse()->daughter(),
            Relationship::fixed(...$rel('posūnis', 'posūnio'))->married()->spouse()->son(),
            Relationship::fixed(...$rel('posūnis', 'posūnio'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$rel('buvusi žmona', 'buvusios žmonos'))->divorced()->partner()->female(),
            Relationship::fixed(...$rel('buvęs vyras', 'buvusio vyro'))->divorced()->partner()->male(),
            Relationship::fixed(...$rel('buvęs sutuoktinis', 'buvusio sutuoktinio'))->divorced()->partner(),
            Relationship::fixed(...$rel('sužadėtinė', 'sužadėtinės'))->engaged()->partner()->female(),
            Relationship::fixed(...$rel('sužadėtinis', 'sužadėtinio'))->engaged()->partner()->male(),
            Relationship::fixed(...$rel('žmona', 'žmonos'))->wife(),
            Relationship::fixed(...$rel('vyras', 'vyro'))->husband(),
            Relationship::fixed(...$rel('sutuoktinis', 'sutuoktinio'))->spouse(),
            Relationship::fixed(...$rel('partneris', 'partnerio'))->partner(),
            // In-laws — wife's parents
            Relationship::fixed(...$rel('uošvė', 'uošvės'))->wife()->mother(),
            Relationship::fixed(...$rel('uošvis', 'uošvio'))->wife()->father(),
            // In-laws — husband's parents
            Relationship::fixed(...$rel('anyta', 'anytos'))->husband()->mother(),
            Relationship::fixed(...$rel('šešuras', 'šešuro'))->husband()->father(),
            // In-laws — spouse's parents (generic)
            Relationship::fixed(...$rel('uošvė', 'uošvės'))->married()->spouse()->mother(),
            Relationship::fixed(...$rel('uošvis', 'uošvio'))->married()->spouse()->father(),
            Relationship::fixed(...$rel('uošvė', 'uošvės'))->spouse()->mother(),
            Relationship::fixed(...$rel('uošvis', 'uošvio'))->spouse()->father(),
            // Children-in-law
            Relationship::fixed(...$rel('marti', 'marčios'))->child()->wife(),
            Relationship::fixed(...$rel('žentas', 'žento'))->child()->husband(),
            // Siblings-in-law (spouse's siblings)
            Relationship::fixed(...$rel('svainė', 'svainės'))->spouse()->sister(),
            Relationship::fixed(...$rel('svainis', 'svainio'))->spouse()->brother(),
            // Siblings-in-law (sibling's spouses)
            Relationship::fixed(...$rel('brolienė', 'brolienės'))->brother()->wife(),
            Relationship::fixed(...$rel('svainis', 'svainio'))->sister()->husband(),
            Relationship::fixed(...$rel('svainė', 'svainės'))->sibling()->wife(),
            Relationship::fixed(...$rel('svainis', 'svainio'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$rel('senelė', 'senelės'))->parent()->mother(),
            Relationship::fixed(...$rel('senelis', 'senelio'))->parent()->father(),
            Relationship::fixed(...$rel('senelė/senelis', 'senelės/senelio'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$rel('anūkė', 'anūkės'))->child()->daughter(),
            Relationship::fixed(...$rel('anūkas', 'anūko'))->child()->son(),
            Relationship::fixed(...$rel('anūkas', 'anūko'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$rel('teta', 'tetos'))->parent()->sister(),
            Relationship::fixed(...$rel('dėdė', 'dėdės'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$rel('dukterėčia', 'dukterėčios'))->sibling()->daughter(),
            Relationship::fixed(...$rel('sūnėnas', 'sūnėno'))->sibling()->son(),
            // Cousins
            Relationship::fixed(...$rel('pusseserė', 'pusseserės'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$rel('pusbrolis', 'pusbrolio'))->parent()->sibling()->son(),
            Relationship::fixed(...$rel('pusbrolis', 'pusbrolio'))->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond (pro- prefix)
            Relationship::dynamic(static fn (int $n) => $pro($n - 2, 'senelė', 'senelės'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $pro($n - 2, 'senelis', 'senelio'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $pro($n - 2, 'senelis', 'senelio'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(static fn (int $n) => $pro($n - 2, 'anūkė', 'anūkės'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pro($n - 2, 'anūkas', 'anūko'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $pro($n - 2, 'anūkas', 'anūko'))->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $pro($n - 1, 'teta', 'tetos'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $pro($n - 1, 'dėdė', 'dėdės'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $pro($n - 1, 'dukterėčia', 'dukterėčios'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pro($n - 1, 'sūnėnas', 'sūnėno'))->sibling()->descendant()->male(),
        ];
    }
}
