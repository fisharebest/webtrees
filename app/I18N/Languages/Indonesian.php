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
use Fisharebest\Webtrees\Enums\Weekday;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;

final readonly class Indonesian extends AbstractLanguage
{
    protected const string    ENDONYM            = 'Indonesia';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'id';
    protected const string    LOCALE_CODE        = 'id_ID@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const Weekday   FIRST_DAY          = Weekday::Sunday;
    protected const string    DATE_ABOUT         = 'tentang %s';
    protected const string    DATE_AFTER         = 'setelah %s';
    protected const string    DATE_BEFORE        = 'sebelum %s';
    protected const string    DATE_BETWEEN_AND   = 'antara %s dan %s';
    protected const string    DATE_CALCULATED    = 'kalkulasi %s';
    protected const string    DATE_ESTIMATED     = 'estimasi %s';
    protected const string    DATE_FROM          = 'dari %s';
    protected const string    DATE_FROM_TO       = 'dari %s ke %s';
    protected const string    DATE_INTERPRETED   = 'penafsiran %s';
    protected const string    DATE_TO            = 'untuk %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'SM';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'M';
    protected const string    LIST_SEPARATOR_AND = ' dan ';
    protected const string    LIST_SEPARATOR_OR  = ' atau ';


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
        'Nopember',
        'Desember',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tisre',
        'Heshvana',
        'Kislep',
        'Tepet',
        'Sifat',
        'Adar 1',
        'Adar 2',
        'Adars',
        'Nisan',
        'Yare',
        'Sipan',
        'Tamud',
        'Av',
        'Eluls',
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
        'hari pelengkap',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Muharam',
        'Sapar',
        'Rabiul Awal',
        'Rabiul Akhir',
        'Jumadil Awal',
        'Jumadil Tsani',
        'Rojab',
        'Sya’ban',
        'Romadhon',
        'Syawal',
        'Dzulqa’dah',
        'Dzulhijjah',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Parpardin',
        'Ordi',
        'Korad',
        'Tear',
        'Murdad',
        'Sahrivar',
        'Meher',
        'Abana',
        'Azars',
        'Hari',
        'Bahmana',
        'Espan',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    public function relationships(): array
    {
        // Indonesian genitive: noun juxtaposition — "%s ibu" = "mother's %s"
        $id = static fn (string $s): array => [$s, '%s ' . $s];

        return [
            // Adopted
            Relationship::fixed(...$id('ibu angkat'))->adoptive()->mother(),
            Relationship::fixed(...$id('ayah angkat'))->adoptive()->father(),
            Relationship::fixed(...$id('orang tua angkat'))->adoptive()->parent(),
            Relationship::fixed(...$id('anak perempuan angkat'))->adopted()->daughter(),
            Relationship::fixed(...$id('anak laki-laki angkat'))->adopted()->son(),
            Relationship::fixed(...$id('anak angkat'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$id('ibu asuh'))->fostering()->mother(),
            Relationship::fixed(...$id('ayah asuh'))->fostering()->father(),
            Relationship::fixed(...$id('orang tua asuh'))->fostering()->parent(),
            Relationship::fixed(...$id('anak perempuan asuh'))->fostered()->daughter(),
            Relationship::fixed(...$id('anak laki-laki asuh'))->fostered()->son(),
            Relationship::fixed(...$id('anak asuh'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$id('ibu'))->mother(),
            Relationship::fixed(...$id('ayah'))->father(),
            Relationship::fixed(...$id('orang tua'))->parent(),
            // Children
            Relationship::fixed(...$id('anak perempuan'))->daughter(),
            Relationship::fixed(...$id('anak laki-laki'))->son(),
            Relationship::fixed(...$id('anak'))->child(),
            // Siblings — elder/younger distinction
            Relationship::fixed(...$id('saudara perempuan kembar'))->twin()->sister(),
            Relationship::fixed(...$id('saudara laki-laki kembar'))->twin()->brother(),
            Relationship::fixed(...$id('saudara kembar'))->twin()->sibling(),
            Relationship::fixed(...$id('kakak perempuan'))->older()->sister(),
            Relationship::fixed(...$id('kakak laki-laki'))->older()->brother(),
            Relationship::fixed(...$id('adik perempuan'))->younger()->sister(),
            Relationship::fixed(...$id('adik laki-laki'))->younger()->brother(),
            Relationship::fixed(...$id('saudara perempuan'))->sister(),
            Relationship::fixed(...$id('saudara laki-laki'))->brother(),
            Relationship::fixed(...$id('saudara'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$id('saudara perempuan seayah'))->father()->daughter(),
            Relationship::fixed(...$id('saudara laki-laki seayah'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$id('saudara perempuan seibu'))->mother()->daughter(),
            Relationship::fixed(...$id('saudara laki-laki seibu'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$id('saudara perempuan tiri'))->parent()->daughter(),
            Relationship::fixed(...$id('saudara laki-laki tiri'))->parent()->son(),
            Relationship::fixed(...$id('saudara tiri'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$id('ibu tiri'))->parent()->wife(),
            Relationship::fixed(...$id('ayah tiri'))->parent()->husband(),
            Relationship::fixed(...$id('anak perempuan tiri'))->married()->spouse()->daughter(),
            Relationship::fixed(...$id('anak laki-laki tiri'))->married()->spouse()->son(),
            Relationship::fixed(...$id('anak tiri'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$id('mantan istri'))->divorced()->partner()->female(),
            Relationship::fixed(...$id('mantan suami'))->divorced()->partner()->male(),
            Relationship::fixed(...$id('mantan pasangan'))->divorced()->partner(),
            Relationship::fixed(...$id('tunangan perempuan'))->engaged()->partner()->female(),
            Relationship::fixed(...$id('tunangan laki-laki'))->engaged()->partner()->male(),
            Relationship::fixed(...$id('istri'))->wife(),
            Relationship::fixed(...$id('suami'))->husband(),
            Relationship::fixed(...$id('pasangan'))->spouse(),
            Relationship::fixed(...$id('pasangan'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$id('mertua perempuan'))->husband()->mother(),
            Relationship::fixed(...$id('mertua laki-laki'))->husband()->father(),
            Relationship::fixed(...$id('mertua perempuan'))->wife()->mother(),
            Relationship::fixed(...$id('mertua laki-laki'))->wife()->father(),
            Relationship::fixed(...$id('mertua'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$id('menantu perempuan'))->child()->wife(),
            Relationship::fixed(...$id('menantu laki-laki'))->child()->husband(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$id('ipar perempuan'))->husband()->sister(),
            Relationship::fixed(...$id('ipar laki-laki'))->husband()->brother(),
            Relationship::fixed(...$id('ipar perempuan'))->wife()->sister(),
            Relationship::fixed(...$id('ipar laki-laki'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$id('ipar perempuan'))->brother()->wife(),
            Relationship::fixed(...$id('ipar laki-laki'))->sister()->husband(),
            // Grandparents
            Relationship::fixed(...$id('nenek'))->parent()->mother(),
            Relationship::fixed(...$id('kakek'))->parent()->father(),
            Relationship::fixed(...$id('kakek/nenek'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$id('cucu perempuan'))->child()->daughter(),
            Relationship::fixed(...$id('cucu laki-laki'))->child()->son(),
            Relationship::fixed(...$id('cucu'))->child()->child(),
            // Aunts/Uncles
            Relationship::fixed(...$id('tante'))->parent()->sister(),
            Relationship::fixed(...$id('paman'))->parent()->brother(),
            Relationship::fixed(...$id('paman/tante'))->parent()->sibling(),
            // Nieces/Nephews
            Relationship::fixed(...$id('keponakan perempuan'))->sibling()->daughter(),
            Relationship::fixed(...$id('keponakan laki-laki'))->sibling()->son(),
            Relationship::fixed(...$id('keponakan'))->sibling()->child(),
            // Cousins — flat (one term for all degrees)
            Relationship::fixed(...$id('sepupu perempuan'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$id('sepupu laki-laki'))->parent()->sibling()->son(),
            Relationship::fixed(...$id('sepupu'))->parent()->sibling()->child(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $id($n > 2 ? 'tante buyut generasi ke-' . $n : 'tante'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $id($n > 2 ? 'paman buyut generasi ke-' . $n : 'paman'))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $id($n > 2 ? 'keponakan perempuan generasi ke-' . $n : 'keponakan perempuan'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $id($n > 2 ? 'keponakan laki-laki generasi ke-' . $n : 'keponakan laki-laki'))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $id($n > 2 ? 'keponakan generasi ke-' . $n : 'keponakan'))->sibling()->descendant(),
            // Dynamic: ancestors — buyut (great-grand), canggah (great-great-grand), then generasi ke-N
            Relationship::dynamic(static fn (int $n) => $id(match (true) {
                $n === 3 => 'nenek buyut',
                $n === 4 => 'nenek canggah',
                default  => 'nenek generasi ke-' . $n,
            }))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $id(match (true) {
                $n === 3 => 'kakek buyut',
                $n === 4 => 'kakek canggah',
                default  => 'kakek generasi ke-' . $n,
            }))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $id(match (true) {
                $n === 3 => 'buyut',
                $n === 4 => 'canggah',
                default  => 'leluhur generasi ke-' . $n,
            }))->ancestor(),
            // Dynamic: descendants — buyut (great-grand), canggah (great-great-grand), then generasi ke-N
            Relationship::dynamic(static fn (int $n) => $id(match (true) {
                $n === 3 => 'cicit perempuan',
                $n === 4 => 'canggah perempuan',
                default  => 'keturunan perempuan generasi ke-' . $n,
            }))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $id(match (true) {
                $n === 3 => 'cicit laki-laki',
                $n === 4 => 'canggah laki-laki',
                default  => 'keturunan laki-laki generasi ke-' . $n,
            }))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $id(match (true) {
                $n === 3 => 'cicit',
                $n === 4 => 'canggah',
                default  => 'keturunan generasi ke-' . $n,
            }))->descendant(),
        ];
    }
}
