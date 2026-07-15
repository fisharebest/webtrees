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

final readonly class Malay extends AbstractLanguage
{
    protected const string    ENDONYM            = 'Melayu';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'ms';
    protected const string    LOCALE_CODE        = 'ms_MY@collation=phonebook';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . ' Sebelum Tahun Masihi';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . ' Sebelum Tahun Masihi';
    protected const string    LIST_SEPARATOR_AND = ' dan ';
    protected const string    LIST_SEPARATOR_OR  = ' atau ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Januari',
        'Februari',
        'Mac',
        'April',
        'Mei',
        'Jun',
        'Julai',
        'Ogos',
        'September',
        'Oktober',
        'November',
        'Disember',
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
        'Rabiulawal',
        'Rabiulakhir',
        'Jamadilawal',
        'Jamadilakhir',
        'Rejab',
        'Syaaban',
        'Ramadan',
        'Syawal',
        'Zulkaedah',
        'Zulhijjah',
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
        // Malay genitive: noun juxtaposition — "%s ibu" = "mother's %s"
        $ms = static fn (string $s): array => [$s, '%s ' . $s];

        return [
            // Adopted
            Relationship::fixed(...$ms('ibu angkat'))->adoptive()->mother(),
            Relationship::fixed(...$ms('bapa angkat'))->adoptive()->father(),
            Relationship::fixed(...$ms('ibu bapa angkat'))->adoptive()->parent(),
            Relationship::fixed(...$ms('anak perempuan angkat'))->adopted()->daughter(),
            Relationship::fixed(...$ms('anak lelaki angkat'))->adopted()->son(),
            Relationship::fixed(...$ms('anak angkat'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$ms('ibu asuh'))->fostering()->mother(),
            Relationship::fixed(...$ms('bapa asuh'))->fostering()->father(),
            Relationship::fixed(...$ms('ibu bapa asuh'))->fostering()->parent(),
            Relationship::fixed(...$ms('anak perempuan asuh'))->fostered()->daughter(),
            Relationship::fixed(...$ms('anak lelaki asuh'))->fostered()->son(),
            Relationship::fixed(...$ms('anak asuh'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$ms('ibu'))->mother(),
            Relationship::fixed(...$ms('bapa'))->father(),
            Relationship::fixed(...$ms('ibu bapa'))->parent(),
            // Children
            Relationship::fixed(...$ms('anak perempuan'))->daughter(),
            Relationship::fixed(...$ms('anak lelaki'))->son(),
            Relationship::fixed(...$ms('anak'))->child(),
            // Siblings — elder/younger distinction
            Relationship::fixed(...$ms('adik-beradik perempuan kembar'))->twin()->sister(),
            Relationship::fixed(...$ms('adik-beradik lelaki kembar'))->twin()->brother(),
            Relationship::fixed(...$ms('adik-beradik kembar'))->twin()->sibling(),
            Relationship::fixed(...$ms('kakak perempuan'))->older()->sister(),
            Relationship::fixed(...$ms('abang'))->older()->brother(),
            Relationship::fixed(...$ms('adik perempuan'))->younger()->sister(),
            Relationship::fixed(...$ms('adik lelaki'))->younger()->brother(),
            Relationship::fixed(...$ms('adik-beradik perempuan'))->sister(),
            Relationship::fixed(...$ms('adik-beradik lelaki'))->brother(),
            Relationship::fixed(...$ms('adik-beradik'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$ms('adik-beradik perempuan sebapa'))->father()->daughter(),
            Relationship::fixed(...$ms('adik-beradik lelaki sebapa'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$ms('adik-beradik perempuan seibu'))->mother()->daughter(),
            Relationship::fixed(...$ms('adik-beradik lelaki seibu'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$ms('adik-beradik perempuan tiri'))->parent()->daughter(),
            Relationship::fixed(...$ms('adik-beradik lelaki tiri'))->parent()->son(),
            Relationship::fixed(...$ms('adik-beradik tiri'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$ms('ibu tiri'))->parent()->wife(),
            Relationship::fixed(...$ms('bapa tiri'))->parent()->husband(),
            Relationship::fixed(...$ms('anak perempuan tiri'))->married()->spouse()->daughter(),
            Relationship::fixed(...$ms('anak lelaki tiri'))->married()->spouse()->son(),
            Relationship::fixed(...$ms('anak tiri'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$ms('bekas isteri'))->divorced()->partner()->female(),
            Relationship::fixed(...$ms('bekas suami'))->divorced()->partner()->male(),
            Relationship::fixed(...$ms('bekas pasangan'))->divorced()->partner(),
            Relationship::fixed(...$ms('tunang perempuan'))->engaged()->partner()->female(),
            Relationship::fixed(...$ms('tunang lelaki'))->engaged()->partner()->male(),
            Relationship::fixed(...$ms('isteri'))->wife(),
            Relationship::fixed(...$ms('suami'))->husband(),
            Relationship::fixed(...$ms('pasangan'))->spouse(),
            Relationship::fixed(...$ms('pasangan'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$ms('ibu mertua'))->husband()->mother(),
            Relationship::fixed(...$ms('bapa mertua'))->husband()->father(),
            Relationship::fixed(...$ms('ibu mertua'))->wife()->mother(),
            Relationship::fixed(...$ms('bapa mertua'))->wife()->father(),
            Relationship::fixed(...$ms('ibu bapa mertua'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$ms('menantu perempuan'))->child()->wife(),
            Relationship::fixed(...$ms('menantu lelaki'))->child()->husband(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$ms('ipar perempuan'))->husband()->sister(),
            Relationship::fixed(...$ms('ipar lelaki'))->husband()->brother(),
            Relationship::fixed(...$ms('ipar perempuan'))->wife()->sister(),
            Relationship::fixed(...$ms('ipar lelaki'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$ms('ipar perempuan'))->brother()->wife(),
            Relationship::fixed(...$ms('ipar lelaki'))->sister()->husband(),
            // Grandparents
            Relationship::fixed(...$ms('nenek'))->parent()->mother(),
            Relationship::fixed(...$ms('datuk'))->parent()->father(),
            Relationship::fixed(...$ms('datuk/nenek'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$ms('cucu perempuan'))->child()->daughter(),
            Relationship::fixed(...$ms('cucu lelaki'))->child()->son(),
            Relationship::fixed(...$ms('cucu'))->child()->child(),
            // Aunts/Uncles
            Relationship::fixed(...$ms('mak saudara'))->parent()->sister(),
            Relationship::fixed(...$ms('pak saudara'))->parent()->brother(),
            Relationship::fixed(...$ms('pak/mak saudara'))->parent()->sibling(),
            // Nieces/Nephews
            Relationship::fixed(...$ms('anak saudara perempuan'))->sibling()->daughter(),
            Relationship::fixed(...$ms('anak saudara lelaki'))->sibling()->son(),
            Relationship::fixed(...$ms('anak saudara'))->sibling()->child(),
            // Cousins — flat (one term for all degrees)
            Relationship::fixed(...$ms('sepupu perempuan'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$ms('sepupu lelaki'))->parent()->sibling()->son(),
            Relationship::fixed(...$ms('sepupu'))->parent()->sibling()->child(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $ms($n > 2 ? 'mak saudara buyut generasi ke-' . $n : 'mak saudara'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $ms($n > 2 ? 'pak saudara buyut generasi ke-' . $n : 'pak saudara'))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $ms($n > 2 ? 'anak saudara perempuan generasi ke-' . $n : 'anak saudara perempuan'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $ms($n > 2 ? 'anak saudara lelaki generasi ke-' . $n : 'anak saudara lelaki'))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $ms($n > 2 ? 'anak saudara generasi ke-' . $n : 'anak saudara'))->sibling()->descendant(),
            // Dynamic: ancestors — buyut (great-grand), moyang (great-great-grand), then generasi ke-N
            Relationship::dynamic(static fn (int $n) => $ms(match (true) {
                $n === 3 => 'nenek buyut',
                $n === 4 => 'nenek moyang',
                default  => 'nenek generasi ke-' . $n,
            }))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $ms(match (true) {
                $n === 3 => 'datuk buyut',
                $n === 4 => 'datuk moyang',
                default  => 'datuk generasi ke-' . $n,
            }))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $ms(match (true) {
                $n === 3 => 'buyut',
                $n === 4 => 'moyang',
                default  => 'nenek moyang generasi ke-' . $n,
            }))->ancestor(),
            // Dynamic: descendants — cicit (great-grand), piut (great-great-grand), then generasi ke-N
            Relationship::dynamic(static fn (int $n) => $ms(match (true) {
                $n === 3 => 'cicit perempuan',
                $n === 4 => 'piut perempuan',
                default  => 'keturunan perempuan generasi ke-' . $n,
            }))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $ms(match (true) {
                $n === 3 => 'cicit lelaki',
                $n === 4 => 'piut lelaki',
                default  => 'keturunan lelaki generasi ke-' . $n,
            }))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $ms(match (true) {
                $n === 3 => 'cicit',
                $n === 4 => 'piut',
                default  => 'keturunan generasi ke-' . $n,
            }))->descendant(),
        ];
    }
}
