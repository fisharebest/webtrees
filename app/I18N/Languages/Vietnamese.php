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

use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;

final readonly class Vietnamese extends AbstractLanguage
{
    protected const string    ENDONYM            = 'Tiếng Việt';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'vi';
    protected const string    LOCALE_CODE        = 'vi_VN@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'khoảng %s';
    protected const string    DATE_AFTER         = 'sau %s';
    protected const string    DATE_BEFORE        = 'trước %s';
    protected const string    DATE_BETWEEN_AND   = 'giữa %s và %s';
    protected const string    DATE_CALCULATED    = 'được tính %s';
    protected const string    DATE_ESTIMATED     = 'ước tính %s';
    protected const string    DATE_FROM          = 'từ %s';
    protected const string    DATE_FROM_TO       = 'từ %s đến %s';
    protected const string    DATE_INTERPRETED   = 'giải thích là %s';
    protected const string    DATE_TO            = 'đến %s';
    protected const string    ERA_BCE            = '%s BCE';
    protected const string    LIST_SEPARATOR_AND = ' và ';
    protected const string    LIST_SEPARATOR_OR  = ' hoặc ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Tháng Giêng',
        'Tháng Hai',
        'Tháng Ba',
        'Tháng Tư',
        'Tháng Năm',
        'Tháng Sáu',
        'Tháng Bảy',
        'Tháng Tám',
        'Tháng Chín',
        'Tháng Mười',
        'Tháng Mười Một',
        'Tháng Mười Hai',
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
        // Vietnamese uses "của" (of) for genitive constructions
        $vi = static fn (string $s): array => [$s, '%s của ' . $s];

        return [
            // Adopted
            Relationship::fixed(...$vi('mẹ nuôi'))->adoptive()->mother(),
            Relationship::fixed(...$vi('bố nuôi'))->adoptive()->father(),
            Relationship::fixed(...$vi('cha/mẹ nuôi'))->adoptive()->parent(),
            Relationship::fixed(...$vi('con gái nuôi'))->adopted()->daughter(),
            Relationship::fixed(...$vi('con trai nuôi'))->adopted()->son(),
            Relationship::fixed(...$vi('con nuôi'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$vi('mẹ đỡ đầu'))->fostering()->mother(),
            Relationship::fixed(...$vi('bố đỡ đầu'))->fostering()->father(),
            Relationship::fixed(...$vi('cha/mẹ đỡ đầu'))->fostering()->parent(),
            Relationship::fixed(...$vi('con gái đỡ đầu'))->fostered()->daughter(),
            Relationship::fixed(...$vi('con trai đỡ đầu'))->fostered()->son(),
            Relationship::fixed(...$vi('con đỡ đầu'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$vi('mẹ'))->mother(),
            Relationship::fixed(...$vi('bố'))->father(),
            Relationship::fixed(...$vi('cha/mẹ'))->parent(),
            // Children
            Relationship::fixed(...$vi('con gái'))->daughter(),
            Relationship::fixed(...$vi('con trai'))->son(),
            Relationship::fixed(...$vi('con'))->child(),
            // Siblings — elder/younger distinction
            Relationship::fixed(...$vi('chị sinh đôi'))->twin()->sister(),
            Relationship::fixed(...$vi('anh sinh đôi'))->twin()->brother(),
            Relationship::fixed(...$vi('sinh đôi'))->twin()->sibling(),
            Relationship::fixed(...$vi('chị'))->older()->sister(),
            Relationship::fixed(...$vi('anh'))->older()->brother(),
            Relationship::fixed(...$vi('em gái'))->younger()->sister(),
            Relationship::fixed(...$vi('em trai'))->younger()->brother(),
            Relationship::fixed(...$vi('chị/em gái'))->sister(),
            Relationship::fixed(...$vi('anh/em trai'))->brother(),
            Relationship::fixed(...$vi('anh chị em'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$vi('chị/em gái cùng cha khác mẹ'))->father()->daughter(),
            Relationship::fixed(...$vi('anh/em trai cùng cha khác mẹ'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$vi('chị/em gái cùng mẹ khác cha'))->mother()->daughter(),
            Relationship::fixed(...$vi('anh/em trai cùng mẹ khác cha'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$vi('chị/em gái khác cha/mẹ'))->parent()->daughter(),
            Relationship::fixed(...$vi('anh/em trai khác cha/mẹ'))->parent()->son(),
            Relationship::fixed(...$vi('anh chị em khác cha/mẹ'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$vi('mẹ kế'))->parent()->wife(),
            Relationship::fixed(...$vi('bố dượng'))->parent()->husband(),
            Relationship::fixed(...$vi('con gái riêng'))->married()->spouse()->daughter(),
            Relationship::fixed(...$vi('con trai riêng'))->married()->spouse()->son(),
            Relationship::fixed(...$vi('con riêng'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$vi('vợ cũ'))->divorced()->partner()->female(),
            Relationship::fixed(...$vi('chồng cũ'))->divorced()->partner()->male(),
            Relationship::fixed(...$vi('vợ/chồng cũ'))->divorced()->partner(),
            Relationship::fixed(...$vi('hôn thê'))->engaged()->partner()->female(),
            Relationship::fixed(...$vi('hôn phu'))->engaged()->partner()->male(),
            Relationship::fixed(...$vi('vợ'))->wife(),
            Relationship::fixed(...$vi('chồng'))->husband(),
            Relationship::fixed(...$vi('vợ/chồng'))->spouse(),
            Relationship::fixed(...$vi('bạn đời'))->partner(),
            // In-laws (spouse's parents — distinguished by spouse's gender)
            Relationship::fixed(...$vi('mẹ chồng'))->husband()->mother(),
            Relationship::fixed(...$vi('bố chồng'))->husband()->father(),
            Relationship::fixed(...$vi('mẹ vợ'))->wife()->mother(),
            Relationship::fixed(...$vi('bố vợ'))->wife()->father(),
            Relationship::fixed(...$vi('cha/mẹ vợ/chồng'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$vi('con dâu'))->child()->wife(),
            Relationship::fixed(...$vi('con rể'))->child()->husband(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$vi('chị/em chồng'))->husband()->sister(),
            Relationship::fixed(...$vi('anh/em chồng'))->husband()->brother(),
            Relationship::fixed(...$vi('chị/em vợ'))->wife()->sister(),
            Relationship::fixed(...$vi('anh/em vợ'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$vi('chị/em dâu'))->brother()->wife(),
            Relationship::fixed(...$vi('anh/em rể'))->sister()->husband(),
            // Grandparents — paternal/maternal distinction
            Relationship::fixed(...$vi('bà nội'))->father()->mother(),
            Relationship::fixed(...$vi('ông nội'))->father()->father(),
            Relationship::fixed(...$vi('bà ngoại'))->mother()->mother(),
            Relationship::fixed(...$vi('ông ngoại'))->mother()->father(),
            Relationship::fixed(...$vi('ông/bà'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$vi('cháu gái'))->child()->daughter(),
            Relationship::fixed(...$vi('cháu trai'))->child()->son(),
            Relationship::fixed(...$vi('cháu'))->child()->child(),
            // Aunts/Uncles — paternal
            Relationship::fixed(...$vi('cô'))->father()->sister(),
            Relationship::fixed(...$vi('chú/bác'))->father()->brother(),
            // Aunts/Uncles — maternal
            Relationship::fixed(...$vi('dì'))->mother()->sister(),
            Relationship::fixed(...$vi('cậu'))->mother()->brother(),
            // Aunts/Uncles — generic fallback
            Relationship::fixed(...$vi('cô/dì'))->parent()->sister(),
            Relationship::fixed(...$vi('chú/bác/cậu'))->parent()->brother(),
            // Nieces/Nephews
            Relationship::fixed(...$vi('cháu gái'))->brother()->daughter(),
            Relationship::fixed(...$vi('cháu trai'))->brother()->son(),
            Relationship::fixed(...$vi('cháu gái'))->sister()->daughter(),
            Relationship::fixed(...$vi('cháu trai'))->sister()->son(),
            Relationship::fixed(...$vi('cháu'))->sibling()->child(),
            // Cousins — with elder/younger distinction
            Relationship::fixed(...$vi('anh họ'))->older()->parent()->sibling()->son(),
            Relationship::fixed(...$vi('chị họ'))->older()->parent()->sibling()->daughter(),
            Relationship::fixed(...$vi('em họ'))->younger()->parent()->sibling()->child(),
            Relationship::fixed(...$vi('anh chị em họ'))->parent()->sibling()->child(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $vi('cô/dì' . ($n > 2 ? ' đời ' . $n : '')))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $vi('chú/bác/cậu' . ($n > 2 ? ' đời ' . $n : '')))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $vi('cháu gái' . ($n > 2 ? ' đời ' . $n : '')))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $vi('cháu trai' . ($n > 2 ? ' đời ' . $n : '')))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $vi('cháu' . ($n > 2 ? ' đời ' . $n : '')))->sibling()->descendant(),
            // Dynamic: ancestors — cụ (great), kỵ (great-great), tổ (beyond)
            Relationship::dynamic(static fn (int $n) => $vi(match (true) {
                $n === 3 => 'cụ bà',
                $n === 4 => 'kỵ bà',
                default  => 'tổ bà đời ' . $n,
            }))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $vi(match (true) {
                $n === 3 => 'cụ ông',
                $n === 4 => 'kỵ ông',
                default  => 'tổ ông đời ' . $n,
            }))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $vi(match (true) {
                $n === 3 => 'cụ',
                $n === 4 => 'kỵ',
                default  => 'tổ tiên đời ' . $n,
            }))->ancestor(),
            // Dynamic: descendants — chắt (great), chút (great-great), chít (beyond)
            Relationship::dynamic(static fn (int $n) => $vi(match (true) {
                $n === 3 => 'chắt gái',
                $n === 4 => 'chút gái',
                default  => 'chít gái đời ' . $n,
            }))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $vi(match (true) {
                $n === 3 => 'chắt trai',
                $n === 4 => 'chút trai',
                default  => 'chít trai đời ' . $n,
            }))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $vi(match (true) {
                $n === 3 => 'chắt',
                $n === 4 => 'chút',
                default  => 'chít đời ' . $n,
            }))->descendant(),
        ];
    }
}
