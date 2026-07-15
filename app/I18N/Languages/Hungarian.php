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

use function mb_substr;
use function str_starts_with;

final readonly class Hungarian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'magyar';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'hu';
    protected const string    LOCALE_CODE        = 'hu_HU@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = '%s körül';
    protected const string    DATE_AFTER         = '%s után';
    protected const string    DATE_BEFORE        = '%s előtt';
    protected const string    DATE_BETWEEN_AND   = '%s és %s között';
    protected const string    DATE_CALCULATED    = 'számított %s';
    protected const string    DATE_ESTIMATED     = 'becsült %s';
    protected const string    DATE_FROM          = 'ettől: %s';
    protected const string    DATE_FROM_TO       = 'ettől: %s eddig: %s';
    protected const string    DATE_INTERPRETED   = 'értelmezhető %s';
    protected const string    DATE_TO            = 'eddig: %s';
    protected const string    ERA_BCE            = 'i.' . UTF8::NO_BREAK_SPACE . 'e.' . UTF8::NO_BREAK_SPACE . '%s';
    protected const string    ERA_CE             = 'i.' . UTF8::NO_BREAK_SPACE . 'u.' . UTF8::NO_BREAK_SPACE . '%s';
    protected const string    LIST_SEPARATOR_AND = ' és ';
    protected const string    LIST_SEPARATOR_OR  = ' vagy ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'január',
        'február',
        'március',
        'április',
        'május',
        'június',
        'július',
        'augusztus',
        'szeptember',
        'október',
        'november',
        'december',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tisri',
        'Hesván',
        'Kiszlév',
        'Tévész',
        'Svát',
        'Ádár risón',
        'dr sni',
        'Ádár',
        'Niszán',
        'Ijár',
        'Sziván',
        'Tamuz',
        'Áv',
        'Elul',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'Szüret hava',
        'Köd hava',
        'Dér hava',
        'Hó hava',
        'Eső hava',
        'Szél hava',
        'Sarjadás hava',
        'Virágzás hava',
        'Rét hava',
        'Aratás hónapja',
        'Hőség hónapja',
        'Gyümölcs hava',
        'extra napok',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Moharrem',
        'Szafar',
        'Rabi’ al-awwal',
        'Rabi’ al-thani',
        'Dsemádi el avvel',
        'Dsemádi el accher',
        'Redseb',
        'Sabán',
        'Ramadán',
        'Sevvál',
        'Dsül kade',
        'Dsül hedse',
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
        UTF8::LATIN_CAPITAL_LETTER_A_WITH_ACUTE,
        'B',
        'C',
        'CS',
        'D',
        'DZ',
        'DZS',
        'E',
        UTF8::LATIN_CAPITAL_LETTER_E_WITH_ACUTE,
        'F',
        'G',
        'GY',
        'H',
        'I',
        UTF8::LATIN_CAPITAL_LETTER_I_WITH_ACUTE,
        'J',
        'K',
        'L',
        'LY',
        'M',
        'N',
        'NY',
        'O',
        UTF8::LATIN_CAPITAL_LETTER_O_WITH_ACUTE,
        UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
        UTF8::LATIN_CAPITAL_LETTER_O_WITH_DOUBLE_ACUTE,
        'P',
        'Q',
        'R',
        'S',
        'SZ',
        'T',
        'TY',
        'U',
        UTF8::LATIN_CAPITAL_LETTER_U_WITH_ACUTE,
        UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS,
        UTF8::LATIN_CAPITAL_LETTER_U_WITH_DOUBLE_ACUTE,
        'V',
        'W',
        'X',
        'Y',
        'Z',
        'ZS',
    ];

    public function assembleDate(string $day, string $month, string $year): string
    {
        $parts = [];

        if ($year !== '') {
            // @TODO - The dot is required - unless we are using a case suffix.
            if ($month !== '') {
                $parts[] = $year . '.';
            } else {
                $parts[] = $year;
            }
        }

        if ($month !== '') {
            $parts[] = $month;
        }

        if ($day !== '') {
            $parts[] = $day;
        }

        return implode(' ', $parts);
    }

    public function dateOrder(): string
    {
        return 'YMD';
    }

    public function initialLetter(string $string): string
    {
        foreach (['CS', 'DZS', 'DZ', 'GY', 'LY', 'NY', 'SZ', 'TY', 'ZS'] as $digraph) {
            if (str_starts_with($string, $digraph)) {
                return $digraph;
            }
        }

        return mb_substr($string, 0, 1);
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_CAPITAL_LETTER_A_WITH_ACUTE,
            'E' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_CAPITAL_LETTER_E_WITH_ACUTE,
            'I' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_CAPITAL_LETTER_I_WITH_ACUTE,
            'O' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_CAPITAL_LETTER_O_WITH_ACUTE,
            'O' . UTF8::COMBINING_DIAERESIS           => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
            'O' . UTF8::COMBINING_DOUBLE_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DOUBLE_ACUTE,
            'U' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_CAPITAL_LETTER_U_WITH_ACUTE,
            'U' . UTF8::COMBINING_DIAERESIS           => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DIAERESIS,
            'U' . UTF8::COMBINING_DOUBLE_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_U_WITH_DOUBLE_ACUTE,
            'a' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_SMALL_LETTER_A_WITH_ACUTE,
            'e' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_SMALL_LETTER_E_WITH_ACUTE,
            'i' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_SMALL_LETTER_I_WITH_ACUTE,
            'o' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_SMALL_LETTER_O_WITH_ACUTE,
            'o' . UTF8::COMBINING_DIAERESIS           => UTF8::LATIN_SMALL_LETTER_O_WITH_DIAERESIS,
            'o' . UTF8::COMBINING_DOUBLE_ACUTE_ACCENT => UTF8::LATIN_SMALL_LETTER_O_WITH_DOUBLE_ACUTE,
            'u' . UTF8::COMBINING_ACUTE_ACCENT        => UTF8::LATIN_SMALL_LETTER_U_WITH_ACUTE,
            'u' . UTF8::COMBINING_DIAERESIS           => UTF8::LATIN_SMALL_LETTER_U_WITH_DIAERESIS,
            'u' . UTF8::COMBINING_DOUBLE_ACUTE_ACCENT => UTF8::LATIN_SMALL_LETTER_U_WITH_DOUBLE_ACUTE,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Hungarian great-grandparent prefixes:
        // n=1: déd (great-grand), n=2: ük (great-great-grand), n=3: szép (3×great)
        // n>3: n×szép- prefix
        $great = static function (int $n, string $nom, string $gen): array {
            if ($n === 1) {
                $prefix = 'déd';
            } elseif ($n === 2) {
                $prefix = 'ük';
            } elseif ($n === 3) {
                $prefix = 'szép';
            } else {
                $prefix = ($n) . '×szép-';
            }

            return [$prefix . $nom, '%s ' . $prefix . $gen];
        };

        return [
            // Parents
            Relationship::fixed('anya', '%s anyja')->mother(),
            Relationship::fixed('apa', '%s apja')->father(),
            Relationship::fixed('szülő', '%s szülője')->parent(),
            // Children
            Relationship::fixed('lánya', '%s lánya')->daughter(),
            Relationship::fixed('fia', '%s fia')->son(),
            Relationship::fixed('gyermek', '%s gyermeke')->child(),
            // Siblings — elder/younger
            Relationship::fixed('ikertestvér', '%s ikertestvére')->twin()->sibling(),
            Relationship::fixed('nővér', '%s nővére')->older()->sister(),
            Relationship::fixed('báty', '%s bátyja')->older()->brother(),
            Relationship::fixed('idősebb testvér', '%s idősebb testvére')->older()->sibling(),
            Relationship::fixed('húg', '%s húga')->younger()->sister(),
            Relationship::fixed('öcs', '%s öccse')->younger()->brother(),
            Relationship::fixed('fiatalabb testvér', '%s fiatalabb testvére')->younger()->sibling(),
            Relationship::fixed('nővér', '%s nővére')->sister(),
            Relationship::fixed('fivér', '%s fivére')->brother(),
            Relationship::fixed('testvér', '%s testvére')->sibling(),
            // Half-siblings
            Relationship::fixed('féltestvér', '%s féltestvére')->parent()->child(),
            // Stepfamily
            Relationship::fixed('mostohaanya', '%s mostohaanyja')->parent()->wife(),
            Relationship::fixed('mostohaapa', '%s mostohaapja')->parent()->husband(),
            Relationship::fixed('mostohaszülő', '%s mostohaszülője')->parent()->married()->spouse(),
            Relationship::fixed('mostohalánya', '%s mostohalánya')->married()->spouse()->daughter(),
            Relationship::fixed('mostohafia', '%s mostohafia')->married()->spouse()->son(),
            Relationship::fixed('mostohagyermek', '%s mostohagyermeke')->married()->spouse()->child(),
            Relationship::fixed('mostohatestvér', '%s mostohatestvére')->parent()->spouse()->child(),
            // Partners
            Relationship::fixed('volt feleség', '%s volt felesége')->divorced()->partner()->female(),
            Relationship::fixed('volt férj', '%s volt férje')->divorced()->partner()->male(),
            Relationship::fixed('volt házastárs', '%s volt házastársa')->divorced()->partner(),
            Relationship::fixed('menyasszony', '%s menyasszonya')->engaged()->partner()->female(),
            Relationship::fixed('vőlegény', '%s vőlegénye')->engaged()->partner()->male(),
            Relationship::fixed('feleség', '%s felesége')->wife(),
            Relationship::fixed('férj', '%s férje')->husband(),
            Relationship::fixed('házastárs', '%s házastársa')->spouse(),
            Relationship::fixed('élettárs', '%s élettársa')->partner(),
            // In-laws
            Relationship::fixed('anyós', '%s anyósa')->married()->spouse()->mother(),
            Relationship::fixed('após', '%s apósa')->married()->spouse()->father(),
            Relationship::fixed('meny', '%s menye')->child()->wife(),
            Relationship::fixed('vő', '%s veje')->child()->husband(),
            Relationship::fixed('sógornő', '%s sógornője')->spouse()->sister(),
            Relationship::fixed('sógor', '%s sógora')->spouse()->brother(),
            Relationship::fixed('sógornő', '%s sógornője')->sibling()->wife(),
            Relationship::fixed('sógor', '%s sógora')->sibling()->husband(),
            // Grandparents
            Relationship::fixed('nagymama', '%s nagymamája')->parent()->mother(),
            Relationship::fixed('nagypapa', '%s nagypapája')->parent()->father(),
            Relationship::fixed('nagyszülő', '%s nagyszülője')->parent()->parent(),
            // Grandchildren
            Relationship::fixed('unoka', '%s unokája')->child()->child(),
            // Aunts and uncles
            Relationship::fixed('nagynéni', '%s nagynénje')->parent()->sister(),
            Relationship::fixed('nagybácsi', '%s nagybácsija')->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed('unokahúg', '%s unokahúga')->sibling()->daughter(),
            Relationship::fixed('unokaöcs', '%s unokaöccse')->sibling()->son(),
            // Cousins
            Relationship::fixed('unokatestvér', '%s unokatestvére')->parent()->sibling()->daughter(),
            Relationship::fixed('unokatestvér', '%s unokatestvére')->parent()->sibling()->son(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'nagymama', 'nagymamája'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'nagypapa', 'nagypapája'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'nagyszülő', 'nagyszülője'))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, 'unoka', 'unokája'))->descendant(),
        ];
    }
}
