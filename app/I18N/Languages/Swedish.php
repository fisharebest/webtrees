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

final readonly class Swedish extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'svenska';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'sv';
    protected const string    LOCALE_CODE        = 'sv_SE@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    NEGATIVE_SYMBOL    = UTF8::MINUS_SIGN;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'ungefär %s';
    protected const string    DATE_AFTER         = 'efter %s';
    protected const string    DATE_BEFORE        = 'före %s';
    protected const string    DATE_BETWEEN_AND   = 'mellan %s och %s';
    protected const string    DATE_CALCULATED    = 'beräknad %s';
    protected const string    DATE_ESTIMATED     = 'uppskattad %s';
    protected const string    DATE_FROM          = 'från %s';
    protected const string    DATE_FROM_TO       = 'från %s till %s';
    protected const string    DATE_INTERPRETED   = 'tolkat %s';
    protected const string    DATE_TO            = 'till %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'fvt';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'evt';
    protected const string    LIST_SEPARATOR_AND = ' och ';
    protected const string    LIST_SEPARATOR_OR  = ' eller ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'januari',
        'februari',
        'mars',
        'april',
        'maj',
        'juni',
        'juli',
        'augusti',
        'september',
        'oktober',
        'november',
        'december',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'tishrei',
        'heshvan',
        'kislev',
        'tevet',
        'shvat',
        'adar I',
        'adar II',
        'adar',
        'nisan',
        'ijar',
        'sivan',
        'tamuz',
        'av',
        'elul',
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
        'Jumada al-akhirah',
        'Rajab',
        'Sha’ban',
        'Ramadan',
        'Shawwal',
        'Dhu l-Qa’dah',
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
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE,
        UTF8::LATIN_CAPITAL_LETTER_A_WITH_DIAERESIS,
        UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
    ];

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_RING_ABOVE => UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE,
            'A' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_CAPITAL_LETTER_A_WITH_DIAERESIS,
            'O' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
            'a' . UTF8::COMBINING_RING_ABOVE => UTF8::LATIN_SMALL_LETTER_A_WITH_RING_ABOVE,
            'a' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_SMALL_LETTER_A_WITH_DIAERESIS,
            'o' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_SMALL_LETTER_O_WITH_DIAERESIS,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Swedish genitive: "-s" suffix
        $gen = static fn (string $s): array => [$s, '%s ' . $s . 's'];

        $great = static fn (int $n, string $prefix, string $suffix): array => [
            $prefix . ($n > 3 ? 'gammel×' . $n . '-' : str_repeat('gammel', $n)) . $suffix,
            '%s ' . $prefix . ($n > 3 ? 'gammel×' . $n . '-' : str_repeat('gammel', $n)) . $suffix . 's',
        ];

        return [
            // Parents
            Relationship::fixed(...$gen('mor'))->mother(),
            Relationship::fixed(...$gen('far'))->father(),
            Relationship::fixed(...$gen('förälder'))->parent(),
            // Children
            Relationship::fixed(...$gen('dotter'))->daughter(),
            Relationship::fixed(...$gen('son'))->son(),
            Relationship::fixed(...$gen('barn'))->child(),
            // Siblings
            Relationship::fixed(...$gen('tvillingsyster'))->twin()->sister(),
            Relationship::fixed(...$gen('tvillingbror'))->twin()->brother(),
            Relationship::fixed(...$gen('tvilling'))->twin()->sibling(),
            Relationship::fixed(...$gen('storasyster'))->older()->sister(),
            Relationship::fixed(...$gen('storebror'))->older()->brother(),
            Relationship::fixed(...$gen('äldre syskon'))->older()->sibling(),
            Relationship::fixed(...$gen('lillasyster'))->younger()->sister(),
            Relationship::fixed(...$gen('lillebror'))->younger()->brother(),
            Relationship::fixed(...$gen('yngre syskon'))->younger()->sibling(),
            Relationship::fixed(...$gen('syster'))->sister(),
            Relationship::fixed(...$gen('bror'))->brother(),
            Relationship::fixed(...$gen('syskon'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$gen('halvsyster'))->parent()->daughter(),
            Relationship::fixed(...$gen('halvbror'))->parent()->son(),
            Relationship::fixed(...$gen('halvsyskon'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$gen('styvmor'))->parent()->wife(),
            Relationship::fixed(...$gen('styvfar'))->parent()->husband(),
            Relationship::fixed(...$gen('styvförälder'))->parent()->married()->spouse(),
            Relationship::fixed(...$gen('styvdotter'))->married()->spouse()->daughter(),
            Relationship::fixed(...$gen('styvson'))->married()->spouse()->son(),
            Relationship::fixed(...$gen('styvbarn'))->married()->spouse()->child(),
            Relationship::fixed(...$gen('styvsyster'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$gen('styvbror'))->parent()->spouse()->son(),
            Relationship::fixed(...$gen('styvsyskon'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$gen('ex-fru'))->divorced()->partner()->female(),
            Relationship::fixed(...$gen('ex-man'))->divorced()->partner()->male(),
            Relationship::fixed(...$gen('ex-make/maka'))->divorced()->partner(),
            Relationship::fixed(...$gen('fästmö'))->engaged()->partner()->female(),
            Relationship::fixed(...$gen('fästman'))->engaged()->partner()->male(),
            Relationship::fixed(...$gen('hustru'))->wife(),
            Relationship::fixed(...$gen('make'))->husband(),
            Relationship::fixed(...$gen('make/maka'))->spouse(),
            Relationship::fixed(...$gen('partner'))->partner(),
            // In-laws
            Relationship::fixed(...$gen('svärmor'))->married()->spouse()->mother(),
            Relationship::fixed(...$gen('svärfar'))->married()->spouse()->father(),
            Relationship::fixed(...$gen('svärförälder'))->married()->spouse()->parent(),
            Relationship::fixed(...$gen('svärdotter'))->child()->wife(),
            Relationship::fixed(...$gen('svärson'))->child()->husband(),
            Relationship::fixed(...$gen('svägerska'))->spouse()->sister(),
            Relationship::fixed(...$gen('svåger'))->spouse()->brother(),
            Relationship::fixed(...$gen('svägerska'))->sibling()->wife(),
            Relationship::fixed(...$gen('svåger'))->sibling()->husband(),
            // Grandparents - maternal/paternal
            Relationship::fixed(...$gen('mormor'))->mother()->mother(),
            Relationship::fixed(...$gen('morfar'))->mother()->father(),
            Relationship::fixed(...$gen('farmor'))->father()->mother(),
            Relationship::fixed(...$gen('farfar'))->father()->father(),
            Relationship::fixed(...$gen('mormor/farmor'))->parent()->mother(),
            Relationship::fixed(...$gen('morfar/farfar'))->parent()->father(),
            Relationship::fixed(...$gen('mor-/farförälder'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$gen('dotterdotter'))->daughter()->daughter(),
            Relationship::fixed(...$gen('dotterson'))->daughter()->son(),
            Relationship::fixed(...$gen('sonson'))->son()->son(),
            Relationship::fixed(...$gen('sondotter'))->son()->daughter(),
            Relationship::fixed(...$gen('barnbarn'))->child()->child(),
            // Aunts and uncles - maternal/paternal
            Relationship::fixed(...$gen('moster'))->mother()->sister(),
            Relationship::fixed(...$gen('morbror'))->mother()->brother(),
            Relationship::fixed(...$gen('faster'))->father()->sister(),
            Relationship::fixed(...$gen('farbror'))->father()->brother(),
            Relationship::fixed(...$gen('tant'))->parent()->sister(),
            Relationship::fixed(...$gen('farbror/morbror'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$gen('brorsdotter'))->brother()->daughter(),
            Relationship::fixed(...$gen('brorson'))->brother()->son(),
            Relationship::fixed(...$gen('systerdotter'))->sister()->daughter(),
            Relationship::fixed(...$gen('systerson'))->sister()->son(),
            // Cousins
            Relationship::fixed(...$gen('kusin'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$gen('kusin'))->parent()->sibling()->son(),
            // Dynamic relationships
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'mormor'))->mother()->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'morfar'))->mother()->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'farmor'))->father()->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'farfar'))->father()->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $great($n - 1, '', 'mor-/farförälder'))->ancestor(),
            Relationship::dynamic(static fn (int $n) => $great($n - 2, '', 'barnbarn'))->descendant(),
        ];
    }
}
