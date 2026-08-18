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

        /** @return array{string, string} */
    private function jv(string $s): array
    {
        return [$s, '%s ' . $s];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->jv('ibu angkat'))->adoptive()->mother(),
            Relationship::fixed(...$this->jv('bapak angkat'))->adoptive()->father(),
            Relationship::fixed(...$this->jv('wong tuwa angkat'))->adoptive()->parent(),
            Relationship::fixed(...$this->jv('anak wadon angkat'))->adopted()->daughter(),
            Relationship::fixed(...$this->jv('anak lanang angkat'))->adopted()->son(),
            Relationship::fixed(...$this->jv('anak angkat'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->jv('ibu asuh'))->fostering()->mother(),
            Relationship::fixed(...$this->jv('bapak asuh'))->fostering()->father(),
            Relationship::fixed(...$this->jv('wong tuwa asuh'))->fostering()->parent(),
            Relationship::fixed(...$this->jv('anak wadon asuh'))->fostered()->daughter(),
            Relationship::fixed(...$this->jv('anak lanang asuh'))->fostered()->son(),
            Relationship::fixed(...$this->jv('anak asuh'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->jv('ibu'))->mother(),
            Relationship::fixed(...$this->jv('bapak'))->father(),
            Relationship::fixed(...$this->jv('wong tuwa'))->parent(),
            // Children
            Relationship::fixed(...$this->jv('anak wadon'))->daughter(),
            Relationship::fixed(...$this->jv('anak lanang'))->son(),
            Relationship::fixed(...$this->jv('anak'))->child(),
            // Siblings — elder/younger distinction
            Relationship::fixed(...$this->jv('sedulur wadon kembar'))->multiple()->sister(),
            Relationship::fixed(...$this->jv('sedulur lanang kembar'))->multiple()->brother(),
            Relationship::fixed(...$this->jv('sedulur kembar'))->multiple()->sibling(),
            Relationship::fixed(...$this->jv('mbakyu'))->older()->sister(),
            Relationship::fixed(...$this->jv('kangmas'))->older()->brother(),
            Relationship::fixed(...$this->jv('adhik wadon'))->younger()->sister(),
            Relationship::fixed(...$this->jv('adhik lanang'))->younger()->brother(),
            Relationship::fixed(...$this->jv('sedulur wadon'))->sister(),
            Relationship::fixed(...$this->jv('sedulur lanang'))->brother(),
            Relationship::fixed(...$this->jv('sedulur'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$this->jv('sedulur wadon sabapak'))->father()->daughter(),
            Relationship::fixed(...$this->jv('sedulur lanang sabapak'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$this->jv('sedulur wadon saibu'))->mother()->daughter(),
            Relationship::fixed(...$this->jv('sedulur lanang saibu'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$this->jv('sedulur kuwalon wadon'))->parent()->daughter(),
            Relationship::fixed(...$this->jv('sedulur kuwalon lanang'))->parent()->son(),
            Relationship::fixed(...$this->jv('sedulur kuwalon'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->jv('ibu kuwalon'))->parent()->wife(),
            Relationship::fixed(...$this->jv('bapak kuwalon'))->parent()->husband(),
            Relationship::fixed(...$this->jv('anak kuwalon wadon'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->jv('anak kuwalon lanang'))->married()->spouse()->son(),
            Relationship::fixed(...$this->jv('anak kuwalon'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->jv('tilas bojo wadon'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->jv('tilas bojo lanang'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->jv('tilas bojo'))->divorced()->partner(),
            Relationship::fixed(...$this->jv('pacangan wadon'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->jv('pacangan lanang'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->jv('bojo wadon'))->wife(),
            Relationship::fixed(...$this->jv('bojo lanang'))->husband(),
            Relationship::fixed(...$this->jv('bojo'))->spouse(),
            Relationship::fixed(...$this->jv('bojo'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$this->jv('maratuwa wadon'))->husband()->mother(),
            Relationship::fixed(...$this->jv('maratuwa lanang'))->husband()->father(),
            Relationship::fixed(...$this->jv('maratuwa wadon'))->wife()->mother(),
            Relationship::fixed(...$this->jv('maratuwa lanang'))->wife()->father(),
            Relationship::fixed(...$this->jv('maratuwa'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$this->jv('mantu wadon'))->child()->wife(),
            Relationship::fixed(...$this->jv('mantu lanang'))->child()->husband(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$this->jv('ipe wadon'))->husband()->sister(),
            Relationship::fixed(...$this->jv('ipe lanang'))->husband()->brother(),
            Relationship::fixed(...$this->jv('ipe wadon'))->wife()->sister(),
            Relationship::fixed(...$this->jv('ipe lanang'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$this->jv('ipe wadon'))->brother()->wife(),
            Relationship::fixed(...$this->jv('ipe lanang'))->sister()->husband(),
            // Grandparents
            Relationship::fixed(...$this->jv('simbah putri'))->parent()->mother(),
            Relationship::fixed(...$this->jv('simbah kakung'))->parent()->father(),
            Relationship::fixed(...$this->jv('simbah'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->jv('putu wadon'))->child()->daughter(),
            Relationship::fixed(...$this->jv('putu lanang'))->child()->son(),
            Relationship::fixed(...$this->jv('putu'))->child()->child(),
            // Aunts/Uncles
            Relationship::fixed(...$this->jv('bulik'))->parent()->sister(),
            Relationship::fixed(...$this->jv('paklik'))->parent()->brother(),
            Relationship::fixed(...$this->jv('paklik/bulik'))->parent()->sibling(),
            // Nieces/Nephews
            Relationship::fixed(...$this->jv('keponakan wadon'))->sibling()->daughter(),
            Relationship::fixed(...$this->jv('keponakan lanang'))->sibling()->son(),
            Relationship::fixed(...$this->jv('keponakan'))->sibling()->child(),
            // Cousins — flat (one term for all degrees)
            Relationship::fixed(...$this->jv('misanan wadon'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->jv('misanan lanang'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->jv('misanan'))->parent()->sibling()->child(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->jv($n > 2 ? 'bulik buyut generasi ke-' . $n : 'bulik'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->jv($n > 2 ? 'paklik buyut generasi ke-' . $n : 'paklik'))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->jv($n > 2 ? 'keponakan wadon generasi ke-' . $n : 'keponakan wadon'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->jv($n > 2 ? 'keponakan lanang generasi ke-' . $n : 'keponakan lanang'))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->jv($n > 2 ? 'keponakan generasi ke-' . $n : 'keponakan'))->sibling()->descendant(),
            // Dynamic: ancestors — buyut (great-grand), canggah (great-great-grand), then generasi ke-N
            Relationship::dynamic(fn (int $n) => $this->jv(match (true) {
                $n === 3 => 'simbah buyut putri',
                $n === 4 => 'simbah canggah putri',
                default  => 'simbah putri generasi ke-' . $n,
            }))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->jv(match (true) {
                $n === 3 => 'simbah buyut kakung',
                $n === 4 => 'simbah canggah kakung',
                default  => 'simbah kakung generasi ke-' . $n,
            }))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->jv(match (true) {
                $n === 3 => 'buyut',
                $n === 4 => 'canggah',
                default  => 'leluhur generasi ke-' . $n,
            }))->ancestor(),
            // Dynamic: descendants — buyut (great-grand), canggah (great-great-grand), then generasi ke-N
            Relationship::dynamic(fn (int $n) => $this->jv(match (true) {
                $n === 3 => 'buyut wadon',
                $n === 4 => 'canggah wadon',
                default  => 'turunan wadon generasi ke-' . $n,
            }))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->jv(match (true) {
                $n === 3 => 'buyut lanang',
                $n === 4 => 'canggah lanang',
                default  => 'turunan lanang generasi ke-' . $n,
            }))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->jv(match (true) {
                $n === 3 => 'buyut',
                $n === 4 => 'canggah',
                default  => 'turunan generasi ke-' . $n,
            }))->descendant(),
        ];
    }
}
