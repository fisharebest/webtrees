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

final readonly class Sundanese extends AbstractLanguage
{
    protected const string    ENDONYM            = 'Basa Sunda';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'su';
    protected const string    LOCALE_CODE = 'su_ID@collation=phonebook';
    protected const array     DIGITS      = [
        0   => UTF8::SUNDANESE_DIGIT_ZERO,
        1   => UTF8::SUNDANESE_DIGIT_ONE,
        2   => UTF8::SUNDANESE_DIGIT_TWO,
        3   => UTF8::SUNDANESE_DIGIT_THREE,
        4   => UTF8::SUNDANESE_DIGIT_FOUR,
        5   => UTF8::SUNDANESE_DIGIT_FIVE,
        6   => UTF8::SUNDANESE_DIGIT_SIX,
        7   => UTF8::SUNDANESE_DIGIT_SEVEN,
        8   => UTF8::SUNDANESE_DIGIT_EIGHT,
        9   => UTF8::SUNDANESE_DIGIT_NINE,
    ];
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const Script    SCRIPT             = Script::Sund;
    protected const Weekday   FIRST_DAY          = Weekday::Sunday;
    protected const string    LIST_SEPARATOR_AND = ' sareng ';
    protected const string    LIST_SEPARATOR_OR  = ' atanapi ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Januari',
        'Pébruari',
        'Maret',
        'April',
        'Méi',
        'Juni',
        'Juli',
        'Agustus',
        'Séptémber',
        'Oktober',
        'Nopémber',
        'Désémber',
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
        // Sundanese genitive: noun juxtaposition — "%s indung" = "mother's %s"
        $su = static fn (string $s): array => [$s, '%s ' . $s];

        return [
            // Adopted
            Relationship::fixed(...$su('indung angkat'))->adoptive()->mother(),
            Relationship::fixed(...$su('bapa angkat'))->adoptive()->father(),
            Relationship::fixed(...$su('kolot angkat'))->adoptive()->parent(),
            Relationship::fixed(...$su('anak awéwé angkat'))->adopted()->daughter(),
            Relationship::fixed(...$su('anak lalaki angkat'))->adopted()->son(),
            Relationship::fixed(...$su('anak angkat'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$su('indung asuh'))->fostering()->mother(),
            Relationship::fixed(...$su('bapa asuh'))->fostering()->father(),
            Relationship::fixed(...$su('kolot asuh'))->fostering()->parent(),
            Relationship::fixed(...$su('anak awéwé asuh'))->fostered()->daughter(),
            Relationship::fixed(...$su('anak lalaki asuh'))->fostered()->son(),
            Relationship::fixed(...$su('anak asuh'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$su('indung'))->mother(),
            Relationship::fixed(...$su('bapa'))->father(),
            Relationship::fixed(...$su('kolot'))->parent(),
            // Children
            Relationship::fixed(...$su('anak awéwé'))->daughter(),
            Relationship::fixed(...$su('anak lalaki'))->son(),
            Relationship::fixed(...$su('anak'))->child(),
            // Siblings — elder/younger distinction
            Relationship::fixed(...$su('dulur awéwé kembar'))->twin()->sister(),
            Relationship::fixed(...$su('dulur lalaki kembar'))->twin()->brother(),
            Relationship::fixed(...$su('dulur kembar'))->twin()->sibling(),
            Relationship::fixed(...$su('lanceuk awéwé'))->older()->sister(),
            Relationship::fixed(...$su('lanceuk lalaki'))->older()->brother(),
            Relationship::fixed(...$su('adi awéwé'))->younger()->sister(),
            Relationship::fixed(...$su('adi lalaki'))->younger()->brother(),
            Relationship::fixed(...$su('dulur awéwé'))->sister(),
            Relationship::fixed(...$su('dulur lalaki'))->brother(),
            Relationship::fixed(...$su('dulur'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$su('dulur awéwé sabapa'))->father()->daughter(),
            Relationship::fixed(...$su('dulur lalaki sabapa'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$su('dulur awéwé saindung'))->mother()->daughter(),
            Relationship::fixed(...$su('dulur lalaki saindung'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$su('dulur téré awéwé'))->parent()->daughter(),
            Relationship::fixed(...$su('dulur téré lalaki'))->parent()->son(),
            Relationship::fixed(...$su('dulur téré'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$su('indung téré'))->parent()->wife(),
            Relationship::fixed(...$su('bapa téré'))->parent()->husband(),
            Relationship::fixed(...$su('anak téré awéwé'))->married()->spouse()->daughter(),
            Relationship::fixed(...$su('anak téré lalaki'))->married()->spouse()->son(),
            Relationship::fixed(...$su('anak téré'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$su('urut pamajikan'))->divorced()->partner()->female(),
            Relationship::fixed(...$su('urut salaki'))->divorced()->partner()->male(),
            Relationship::fixed(...$su('urut pasangan'))->divorced()->partner(),
            Relationship::fixed(...$su('tunangan awéwé'))->engaged()->partner()->female(),
            Relationship::fixed(...$su('tunangan lalaki'))->engaged()->partner()->male(),
            Relationship::fixed(...$su('pamajikan'))->wife(),
            Relationship::fixed(...$su('salaki'))->husband(),
            Relationship::fixed(...$su('pasangan'))->spouse(),
            Relationship::fixed(...$su('pasangan'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$su('mitoha awéwé'))->husband()->mother(),
            Relationship::fixed(...$su('mitoha lalaki'))->husband()->father(),
            Relationship::fixed(...$su('mitoha awéwé'))->wife()->mother(),
            Relationship::fixed(...$su('mitoha lalaki'))->wife()->father(),
            Relationship::fixed(...$su('mitoha'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$su('minantu awéwé'))->child()->wife(),
            Relationship::fixed(...$su('minantu lalaki'))->child()->husband(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$su('ipar awéwé'))->husband()->sister(),
            Relationship::fixed(...$su('ipar lalaki'))->husband()->brother(),
            Relationship::fixed(...$su('ipar awéwé'))->wife()->sister(),
            Relationship::fixed(...$su('ipar lalaki'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$su('ipar awéwé'))->brother()->wife(),
            Relationship::fixed(...$su('ipar lalaki'))->sister()->husband(),
            // Grandparents
            Relationship::fixed(...$su('nini'))->parent()->mother(),
            Relationship::fixed(...$su('aki'))->parent()->father(),
            Relationship::fixed(...$su('aki/nini'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$su('incu awéwé'))->child()->daughter(),
            Relationship::fixed(...$su('incu lalaki'))->child()->son(),
            Relationship::fixed(...$su('incu'))->child()->child(),
            // Aunts/Uncles
            Relationship::fixed(...$su('bibi'))->parent()->sister(),
            Relationship::fixed(...$su('mamang'))->parent()->brother(),
            Relationship::fixed(...$su('mamang/bibi'))->parent()->sibling(),
            // Nieces/Nephews
            Relationship::fixed(...$su('kaponakan awéwé'))->sibling()->daughter(),
            Relationship::fixed(...$su('kaponakan lalaki'))->sibling()->son(),
            Relationship::fixed(...$su('kaponakan'))->sibling()->child(),
            // Cousins — flat (one term for all degrees)
            Relationship::fixed(...$su('dulur misan awéwé'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$su('dulur misan lalaki'))->parent()->sibling()->son(),
            Relationship::fixed(...$su('dulur misan'))->parent()->sibling()->child(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $su($n > 2 ? 'bibi buyut generasi ka-' . $n : 'bibi'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $su($n > 2 ? 'mamang buyut generasi ka-' . $n : 'mamang'))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $su($n > 2 ? 'kaponakan awéwé generasi ka-' . $n : 'kaponakan awéwé'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $su($n > 2 ? 'kaponakan lalaki generasi ka-' . $n : 'kaponakan lalaki'))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $su($n > 2 ? 'kaponakan generasi ka-' . $n : 'kaponakan'))->sibling()->descendant(),
            // Dynamic: ancestors — buyut (great-grand), bao (great-great-grand), then generasi ka-N
            Relationship::dynamic(static fn (int $n) => $su(match (true) {
                $n === 3 => 'nini buyut',
                $n === 4 => 'nini bao',
                default  => 'nini generasi ka-' . $n,
            }))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $su(match (true) {
                $n === 3 => 'aki buyut',
                $n === 4 => 'aki bao',
                default  => 'aki generasi ka-' . $n,
            }))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $su(match (true) {
                $n === 3 => 'buyut',
                $n === 4 => 'bao',
                default  => 'karuhun generasi ka-' . $n,
            }))->ancestor(),
            // Dynamic: descendants — buyut (great-grand), bao (great-great-grand), then generasi ka-N
            Relationship::dynamic(static fn (int $n) => $su(match (true) {
                $n === 3 => 'buyut awéwé',
                $n === 4 => 'bao awéwé',
                default  => 'turunan awéwé generasi ka-' . $n,
            }))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $su(match (true) {
                $n === 3 => 'buyut lalaki',
                $n === 4 => 'bao lalaki',
                default  => 'turunan lalaki generasi ka-' . $n,
            }))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $su(match (true) {
                $n === 3 => 'buyut',
                $n === 4 => 'bao',
                default  => 'turunan generasi ka-' . $n,
            }))->descendant(),
        ];
    }
}
