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
use Fisharebest\Webtrees\Enums\Script;
use Fisharebest\Webtrees\Enums\Weekday;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

final readonly class Javanese extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'Jawa';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'jv';
    protected const string    LOCALE_CODE = 'jv_ID@collation=phonebook';
    protected const array     DIGITS      = [
        0   => UTF8::JAVANESE_DIGIT_ZERO,
        1   => UTF8::JAVANESE_DIGIT_ONE,
        2   => UTF8::JAVANESE_DIGIT_TWO,
        3   => UTF8::JAVANESE_DIGIT_THREE,
        4   => UTF8::JAVANESE_DIGIT_FOUR,
        5   => UTF8::JAVANESE_DIGIT_FIVE,
        6   => UTF8::JAVANESE_DIGIT_SIX,
        7   => UTF8::JAVANESE_DIGIT_SEVEN,
        8   => UTF8::JAVANESE_DIGIT_EIGHT,
        9   => UTF8::JAVANESE_DIGIT_NINE,
    ];
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const Script    SCRIPT             = Script::Java;
    protected const Weekday   FIRST_DAY          = Weekday::Sunday;
    protected const string    LIST_SEPARATOR_AND = ' lan ';
    protected const string    LIST_SEPARATOR_OR  = ' utawa ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
    ];


    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

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

    public function relationships(): array
    {
        // Javanese genitive: noun juxtaposition — "%s ibu" = "mother's %s"
        $jv = static fn (string $s): array => [$s, '%s ' . $s];

        return [
            // Adopted
            Relationship::fixed(...$jv('ibu angkat'))->adoptive()->mother(),
            Relationship::fixed(...$jv('bapak angkat'))->adoptive()->father(),
            Relationship::fixed(...$jv('wong tuwa angkat'))->adoptive()->parent(),
            Relationship::fixed(...$jv('anak wadon angkat'))->adopted()->daughter(),
            Relationship::fixed(...$jv('anak lanang angkat'))->adopted()->son(),
            Relationship::fixed(...$jv('anak angkat'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$jv('ibu asuh'))->fostering()->mother(),
            Relationship::fixed(...$jv('bapak asuh'))->fostering()->father(),
            Relationship::fixed(...$jv('wong tuwa asuh'))->fostering()->parent(),
            Relationship::fixed(...$jv('anak wadon asuh'))->fostered()->daughter(),
            Relationship::fixed(...$jv('anak lanang asuh'))->fostered()->son(),
            Relationship::fixed(...$jv('anak asuh'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$jv('ibu'))->mother(),
            Relationship::fixed(...$jv('bapak'))->father(),
            Relationship::fixed(...$jv('wong tuwa'))->parent(),
            // Children
            Relationship::fixed(...$jv('anak wadon'))->daughter(),
            Relationship::fixed(...$jv('anak lanang'))->son(),
            Relationship::fixed(...$jv('anak'))->child(),
            // Siblings — elder/younger distinction
            Relationship::fixed(...$jv('sedulur wadon kembar'))->twin()->sister(),
            Relationship::fixed(...$jv('sedulur lanang kembar'))->twin()->brother(),
            Relationship::fixed(...$jv('sedulur kembar'))->twin()->sibling(),
            Relationship::fixed(...$jv('mbakyu'))->older()->sister(),
            Relationship::fixed(...$jv('kangmas'))->older()->brother(),
            Relationship::fixed(...$jv('adhik wadon'))->younger()->sister(),
            Relationship::fixed(...$jv('adhik lanang'))->younger()->brother(),
            Relationship::fixed(...$jv('sedulur wadon'))->sister(),
            Relationship::fixed(...$jv('sedulur lanang'))->brother(),
            Relationship::fixed(...$jv('sedulur'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$jv('sedulur wadon sabapak'))->father()->daughter(),
            Relationship::fixed(...$jv('sedulur lanang sabapak'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$jv('sedulur wadon saibu'))->mother()->daughter(),
            Relationship::fixed(...$jv('sedulur lanang saibu'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$jv('sedulur kuwalon wadon'))->parent()->daughter(),
            Relationship::fixed(...$jv('sedulur kuwalon lanang'))->parent()->son(),
            Relationship::fixed(...$jv('sedulur kuwalon'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$jv('ibu kuwalon'))->parent()->wife(),
            Relationship::fixed(...$jv('bapak kuwalon'))->parent()->husband(),
            Relationship::fixed(...$jv('anak kuwalon wadon'))->married()->spouse()->daughter(),
            Relationship::fixed(...$jv('anak kuwalon lanang'))->married()->spouse()->son(),
            Relationship::fixed(...$jv('anak kuwalon'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$jv('tilas bojo wadon'))->divorced()->partner()->female(),
            Relationship::fixed(...$jv('tilas bojo lanang'))->divorced()->partner()->male(),
            Relationship::fixed(...$jv('tilas bojo'))->divorced()->partner(),
            Relationship::fixed(...$jv('pacangan wadon'))->engaged()->partner()->female(),
            Relationship::fixed(...$jv('pacangan lanang'))->engaged()->partner()->male(),
            Relationship::fixed(...$jv('bojo wadon'))->wife(),
            Relationship::fixed(...$jv('bojo lanang'))->husband(),
            Relationship::fixed(...$jv('bojo'))->spouse(),
            Relationship::fixed(...$jv('bojo'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$jv('maratuwa wadon'))->husband()->mother(),
            Relationship::fixed(...$jv('maratuwa lanang'))->husband()->father(),
            Relationship::fixed(...$jv('maratuwa wadon'))->wife()->mother(),
            Relationship::fixed(...$jv('maratuwa lanang'))->wife()->father(),
            Relationship::fixed(...$jv('maratuwa'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$jv('mantu wadon'))->child()->wife(),
            Relationship::fixed(...$jv('mantu lanang'))->child()->husband(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$jv('ipe wadon'))->husband()->sister(),
            Relationship::fixed(...$jv('ipe lanang'))->husband()->brother(),
            Relationship::fixed(...$jv('ipe wadon'))->wife()->sister(),
            Relationship::fixed(...$jv('ipe lanang'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$jv('ipe wadon'))->brother()->wife(),
            Relationship::fixed(...$jv('ipe lanang'))->sister()->husband(),
            // Grandparents
            Relationship::fixed(...$jv('simbah putri'))->parent()->mother(),
            Relationship::fixed(...$jv('simbah kakung'))->parent()->father(),
            Relationship::fixed(...$jv('simbah'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$jv('putu wadon'))->child()->daughter(),
            Relationship::fixed(...$jv('putu lanang'))->child()->son(),
            Relationship::fixed(...$jv('putu'))->child()->child(),
            // Aunts/Uncles
            Relationship::fixed(...$jv('bulik'))->parent()->sister(),
            Relationship::fixed(...$jv('paklik'))->parent()->brother(),
            Relationship::fixed(...$jv('paklik/bulik'))->parent()->sibling(),
            // Nieces/Nephews
            Relationship::fixed(...$jv('keponakan wadon'))->sibling()->daughter(),
            Relationship::fixed(...$jv('keponakan lanang'))->sibling()->son(),
            Relationship::fixed(...$jv('keponakan'))->sibling()->child(),
            // Cousins — flat (one term for all degrees)
            Relationship::fixed(...$jv('misanan wadon'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$jv('misanan lanang'))->parent()->sibling()->son(),
            Relationship::fixed(...$jv('misanan'))->parent()->sibling()->child(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $jv($n > 2 ? 'bulik buyut generasi ke-' . $n : 'bulik'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $jv($n > 2 ? 'paklik buyut generasi ke-' . $n : 'paklik'))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $jv($n > 2 ? 'keponakan wadon generasi ke-' . $n : 'keponakan wadon'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $jv($n > 2 ? 'keponakan lanang generasi ke-' . $n : 'keponakan lanang'))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $jv($n > 2 ? 'keponakan generasi ke-' . $n : 'keponakan'))->sibling()->descendant(),
            // Dynamic: ancestors — buyut (great-grand), canggah (great-great-grand), then generasi ke-N
            Relationship::dynamic(static fn (int $n) => $jv(match (true) {
                $n === 3 => 'simbah buyut putri',
                $n === 4 => 'simbah canggah putri',
                default  => 'simbah putri generasi ke-' . $n,
            }))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $jv(match (true) {
                $n === 3 => 'simbah buyut kakung',
                $n === 4 => 'simbah canggah kakung',
                default  => 'simbah kakung generasi ke-' . $n,
            }))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $jv(match (true) {
                $n === 3 => 'buyut',
                $n === 4 => 'canggah',
                default  => 'leluhur generasi ke-' . $n,
            }))->ancestor(),
            // Dynamic: descendants — buyut (great-grand), canggah (great-great-grand), then generasi ke-N
            Relationship::dynamic(static fn (int $n) => $jv(match (true) {
                $n === 3 => 'buyut wadon',
                $n === 4 => 'canggah wadon',
                default  => 'turunan wadon generasi ke-' . $n,
            }))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $jv(match (true) {
                $n === 3 => 'buyut lanang',
                $n === 4 => 'canggah lanang',
                default  => 'turunan lanang generasi ke-' . $n,
            }))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $jv(match (true) {
                $n === 3 => 'buyut',
                $n === 4 => 'canggah',
                default  => 'turunan generasi ke-' . $n,
            }))->descendant(),
        ];
    }
}
