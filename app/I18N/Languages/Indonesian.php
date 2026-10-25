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

        /** @return array{string, string} */
    private function id(string $s): array
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
            Relationship::fixed(...$this->id('ibu angkat'))->adoptive()->mother(),
            Relationship::fixed(...$this->id('ayah angkat'))->adoptive()->father(),
            Relationship::fixed(...$this->id('orang tua angkat'))->adoptive()->parent(),
            Relationship::fixed(...$this->id('anak perempuan angkat'))->adopted()->daughter(),
            Relationship::fixed(...$this->id('anak laki-laki angkat'))->adopted()->son(),
            Relationship::fixed(...$this->id('anak angkat'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->id('ibu asuh'))->fostering()->mother(),
            Relationship::fixed(...$this->id('ayah asuh'))->fostering()->father(),
            Relationship::fixed(...$this->id('orang tua asuh'))->fostering()->parent(),
            Relationship::fixed(...$this->id('anak perempuan asuh'))->fostered()->daughter(),
            Relationship::fixed(...$this->id('anak laki-laki asuh'))->fostered()->son(),
            Relationship::fixed(...$this->id('anak asuh'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->id('ibu'))->mother(),
            Relationship::fixed(...$this->id('ayah'))->father(),
            Relationship::fixed(...$this->id('orang tua'))->parent(),
            // Children
            Relationship::fixed(...$this->id('anak perempuan'))->daughter(),
            Relationship::fixed(...$this->id('anak laki-laki'))->son(),
            Relationship::fixed(...$this->id('anak'))->child(),
            // Siblings — elder/younger distinction
            Relationship::fixed(...$this->id('saudara perempuan kembar'))->multiple()->sister(),
            Relationship::fixed(...$this->id('saudara laki-laki kembar'))->multiple()->brother(),
            Relationship::fixed(...$this->id('saudara kembar'))->multiple()->sibling(),
            Relationship::fixed(...$this->id('kakak perempuan'))->older()->sister(),
            Relationship::fixed(...$this->id('kakak laki-laki'))->older()->brother(),
            Relationship::fixed(...$this->id('adik perempuan'))->younger()->sister(),
            Relationship::fixed(...$this->id('adik laki-laki'))->younger()->brother(),
            Relationship::fixed(...$this->id('saudara perempuan'))->sister(),
            Relationship::fixed(...$this->id('saudara laki-laki'))->brother(),
            Relationship::fixed(...$this->id('saudara'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$this->id('saudara perempuan seayah'))->father()->daughter(),
            Relationship::fixed(...$this->id('saudara laki-laki seayah'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$this->id('saudara perempuan seibu'))->mother()->daughter(),
            Relationship::fixed(...$this->id('saudara laki-laki seibu'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$this->id('saudara perempuan tiri'))->parent()->daughter(),
            Relationship::fixed(...$this->id('saudara laki-laki tiri'))->parent()->son(),
            Relationship::fixed(...$this->id('saudara tiri'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->id('ibu tiri'))->parent()->wife(),
            Relationship::fixed(...$this->id('ayah tiri'))->parent()->husband(),
            Relationship::fixed(...$this->id('anak perempuan tiri'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->id('anak laki-laki tiri'))->married()->spouse()->son(),
            Relationship::fixed(...$this->id('anak tiri'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->id('mantan istri'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->id('mantan suami'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->id('mantan pasangan'))->divorced()->partner(),
            Relationship::fixed(...$this->id('tunangan perempuan'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->id('tunangan laki-laki'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->id('istri'))->wife(),
            Relationship::fixed(...$this->id('suami'))->husband(),
            Relationship::fixed(...$this->id('pasangan'))->spouse(),
            Relationship::fixed(...$this->id('pasangan'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$this->id('mertua perempuan'))->husband()->mother(),
            Relationship::fixed(...$this->id('mertua laki-laki'))->husband()->father(),
            Relationship::fixed(...$this->id('mertua perempuan'))->wife()->mother(),
            Relationship::fixed(...$this->id('mertua laki-laki'))->wife()->father(),
            Relationship::fixed(...$this->id('mertua'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$this->id('menantu perempuan'))->child()->wife(),
            Relationship::fixed(...$this->id('menantu laki-laki'))->child()->husband(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$this->id('ipar perempuan'))->husband()->sister(),
            Relationship::fixed(...$this->id('ipar laki-laki'))->husband()->brother(),
            Relationship::fixed(...$this->id('ipar perempuan'))->wife()->sister(),
            Relationship::fixed(...$this->id('ipar laki-laki'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$this->id('ipar perempuan'))->brother()->wife(),
            Relationship::fixed(...$this->id('ipar laki-laki'))->sister()->husband(),
            // Grandparents
            Relationship::fixed(...$this->id('nenek'))->parent()->mother(),
            Relationship::fixed(...$this->id('kakek'))->parent()->father(),
            Relationship::fixed(...$this->id('kakek/nenek'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->id('cucu perempuan'))->child()->daughter(),
            Relationship::fixed(...$this->id('cucu laki-laki'))->child()->son(),
            Relationship::fixed(...$this->id('cucu'))->child()->child(),
            // Aunts/Uncles
            Relationship::fixed(...$this->id('tante'))->parent()->sister(),
            Relationship::fixed(...$this->id('paman'))->parent()->brother(),
            Relationship::fixed(...$this->id('paman/tante'))->parent()->sibling(),
            // Nieces/Nephews
            Relationship::fixed(...$this->id('keponakan perempuan'))->sibling()->daughter(),
            Relationship::fixed(...$this->id('keponakan laki-laki'))->sibling()->son(),
            Relationship::fixed(...$this->id('keponakan'))->sibling()->child(),
            // Cousins — flat (one term for all degrees)
            Relationship::fixed(...$this->id('sepupu perempuan'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->id('sepupu laki-laki'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->id('sepupu'))->parent()->sibling()->child(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->id($n > 2 ? 'tante buyut generasi ke-' . $n : 'tante'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->id($n > 2 ? 'paman buyut generasi ke-' . $n : 'paman'))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->id($n > 2 ? 'keponakan perempuan generasi ke-' . $n : 'keponakan perempuan'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->id($n > 2 ? 'keponakan laki-laki generasi ke-' . $n : 'keponakan laki-laki'))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->id($n > 2 ? 'keponakan generasi ke-' . $n : 'keponakan'))->sibling()->descendant(),
            // Dynamic: ancestors — buyut (great-grand), canggah (great-great-grand), then generasi ke-N
            Relationship::dynamic(fn (int $n) => $this->id(match (true) {
                $n === 3 => 'nenek buyut',
                $n === 4 => 'nenek canggah',
                default  => 'nenek generasi ke-' . $n,
            }))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->id(match (true) {
                $n === 3 => 'kakek buyut',
                $n === 4 => 'kakek canggah',
                default  => 'kakek generasi ke-' . $n,
            }))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->id(match (true) {
                $n === 3 => 'buyut',
                $n === 4 => 'canggah',
                default  => 'leluhur generasi ke-' . $n,
            }))->ancestor(),
            // Dynamic: descendants — buyut (great-grand), canggah (great-great-grand), then generasi ke-N
            Relationship::dynamic(fn (int $n) => $this->id(match (true) {
                $n === 3 => 'cicit perempuan',
                $n === 4 => 'canggah perempuan',
                default  => 'keturunan perempuan generasi ke-' . $n,
            }))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->id(match (true) {
                $n === 3 => 'cicit laki-laki',
                $n === 4 => 'canggah laki-laki',
                default  => 'keturunan laki-laki generasi ke-' . $n,
            }))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->id(match (true) {
                $n === 3 => 'cicit',
                $n === 4 => 'canggah',
                default  => 'keturunan generasi ke-' . $n,
            }))->descendant(),
        ];
    }
}
