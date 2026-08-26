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

        /** @return array{string, string} */
    private function vi(string $s): array
    {
        return [$s, '%s của ' . $s];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->vi('mẹ nuôi'))->adoptive()->mother(),
            Relationship::fixed(...$this->vi('bố nuôi'))->adoptive()->father(),
            Relationship::fixed(...$this->vi('cha/mẹ nuôi'))->adoptive()->parent(),
            Relationship::fixed(...$this->vi('con gái nuôi'))->adopted()->daughter(),
            Relationship::fixed(...$this->vi('con trai nuôi'))->adopted()->son(),
            Relationship::fixed(...$this->vi('con nuôi'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->vi('mẹ đỡ đầu'))->fostering()->mother(),
            Relationship::fixed(...$this->vi('bố đỡ đầu'))->fostering()->father(),
            Relationship::fixed(...$this->vi('cha/mẹ đỡ đầu'))->fostering()->parent(),
            Relationship::fixed(...$this->vi('con gái đỡ đầu'))->fostered()->daughter(),
            Relationship::fixed(...$this->vi('con trai đỡ đầu'))->fostered()->son(),
            Relationship::fixed(...$this->vi('con đỡ đầu'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->vi('mẹ'))->mother(),
            Relationship::fixed(...$this->vi('bố'))->father(),
            Relationship::fixed(...$this->vi('cha/mẹ'))->parent(),
            // Children
            Relationship::fixed(...$this->vi('con gái'))->daughter(),
            Relationship::fixed(...$this->vi('con trai'))->son(),
            Relationship::fixed(...$this->vi('con'))->child(),
            // Siblings — elder/younger distinction
            Relationship::fixed(...$this->vi('chị sinh đôi'))->multiple()->sister(),
            Relationship::fixed(...$this->vi('anh sinh đôi'))->multiple()->brother(),
            Relationship::fixed(...$this->vi('sinh đôi'))->multiple()->sibling(),
            Relationship::fixed(...$this->vi('chị'))->older()->sister(),
            Relationship::fixed(...$this->vi('anh'))->older()->brother(),
            Relationship::fixed(...$this->vi('em gái'))->younger()->sister(),
            Relationship::fixed(...$this->vi('em trai'))->younger()->brother(),
            Relationship::fixed(...$this->vi('chị/em gái'))->sister(),
            Relationship::fixed(...$this->vi('anh/em trai'))->brother(),
            Relationship::fixed(...$this->vi('anh chị em'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$this->vi('chị/em gái cùng cha khác mẹ'))->father()->daughter(),
            Relationship::fixed(...$this->vi('anh/em trai cùng cha khác mẹ'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$this->vi('chị/em gái cùng mẹ khác cha'))->mother()->daughter(),
            Relationship::fixed(...$this->vi('anh/em trai cùng mẹ khác cha'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$this->vi('chị/em gái khác cha/mẹ'))->parent()->daughter(),
            Relationship::fixed(...$this->vi('anh/em trai khác cha/mẹ'))->parent()->son(),
            Relationship::fixed(...$this->vi('anh chị em khác cha/mẹ'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->vi('mẹ kế'))->parent()->wife(),
            Relationship::fixed(...$this->vi('bố dượng'))->parent()->husband(),
            Relationship::fixed(...$this->vi('con gái riêng'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->vi('con trai riêng'))->married()->spouse()->son(),
            Relationship::fixed(...$this->vi('con riêng'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->vi('vợ cũ'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->vi('chồng cũ'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->vi('vợ/chồng cũ'))->divorced()->partner(),
            Relationship::fixed(...$this->vi('hôn thê'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->vi('hôn phu'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->vi('vợ'))->wife(),
            Relationship::fixed(...$this->vi('chồng'))->husband(),
            Relationship::fixed(...$this->vi('vợ/chồng'))->spouse(),
            Relationship::fixed(...$this->vi('bạn đời'))->partner(),
            // In-laws (spouse's parents — distinguished by spouse's gender)
            Relationship::fixed(...$this->vi('mẹ chồng'))->husband()->mother(),
            Relationship::fixed(...$this->vi('bố chồng'))->husband()->father(),
            Relationship::fixed(...$this->vi('mẹ vợ'))->wife()->mother(),
            Relationship::fixed(...$this->vi('bố vợ'))->wife()->father(),
            Relationship::fixed(...$this->vi('cha/mẹ vợ/chồng'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$this->vi('con dâu'))->child()->wife(),
            Relationship::fixed(...$this->vi('con rể'))->child()->husband(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$this->vi('chị/em chồng'))->husband()->sister(),
            Relationship::fixed(...$this->vi('anh/em chồng'))->husband()->brother(),
            Relationship::fixed(...$this->vi('chị/em vợ'))->wife()->sister(),
            Relationship::fixed(...$this->vi('anh/em vợ'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$this->vi('chị/em dâu'))->brother()->wife(),
            Relationship::fixed(...$this->vi('anh/em rể'))->sister()->husband(),
            // Grandparents — paternal/maternal distinction
            Relationship::fixed(...$this->vi('bà nội'))->father()->mother(),
            Relationship::fixed(...$this->vi('ông nội'))->father()->father(),
            Relationship::fixed(...$this->vi('bà ngoại'))->mother()->mother(),
            Relationship::fixed(...$this->vi('ông ngoại'))->mother()->father(),
            Relationship::fixed(...$this->vi('ông/bà'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->vi('cháu gái'))->child()->daughter(),
            Relationship::fixed(...$this->vi('cháu trai'))->child()->son(),
            Relationship::fixed(...$this->vi('cháu'))->child()->child(),
            // Aunts/Uncles — paternal
            Relationship::fixed(...$this->vi('cô'))->father()->sister(),
            Relationship::fixed(...$this->vi('chú/bác'))->father()->brother(),
            // Aunts/Uncles — maternal
            Relationship::fixed(...$this->vi('dì'))->mother()->sister(),
            Relationship::fixed(...$this->vi('cậu'))->mother()->brother(),
            // Aunts/Uncles — generic fallback
            Relationship::fixed(...$this->vi('cô/dì'))->parent()->sister(),
            Relationship::fixed(...$this->vi('chú/bác/cậu'))->parent()->brother(),
            // Nieces/Nephews
            Relationship::fixed(...$this->vi('cháu gái'))->brother()->daughter(),
            Relationship::fixed(...$this->vi('cháu trai'))->brother()->son(),
            Relationship::fixed(...$this->vi('cháu gái'))->sister()->daughter(),
            Relationship::fixed(...$this->vi('cháu trai'))->sister()->son(),
            Relationship::fixed(...$this->vi('cháu'))->sibling()->child(),
            // Cousins — with elder/younger distinction
            Relationship::fixed(...$this->vi('anh họ'))->older()->parent()->sibling()->son(),
            Relationship::fixed(...$this->vi('chị họ'))->older()->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->vi('em họ'))->younger()->parent()->sibling()->child(),
            Relationship::fixed(...$this->vi('anh chị em họ'))->parent()->sibling()->child(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->vi('cô/dì' . ($n > 2 ? ' đời ' . $n : '')))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->vi('chú/bác/cậu' . ($n > 2 ? ' đời ' . $n : '')))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->vi('cháu gái' . ($n > 2 ? ' đời ' . $n : '')))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->vi('cháu trai' . ($n > 2 ? ' đời ' . $n : '')))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->vi('cháu' . ($n > 2 ? ' đời ' . $n : '')))->sibling()->descendant(),
            // Dynamic: ancestors — cụ (great), kỵ (great-great), tổ (beyond)
            Relationship::dynamic(fn (int $n) => $this->vi(match (true) {
                $n === 3 => 'cụ bà',
                $n === 4 => 'kỵ bà',
                default  => 'tổ bà đời ' . $n,
            }))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->vi(match (true) {
                $n === 3 => 'cụ ông',
                $n === 4 => 'kỵ ông',
                default  => 'tổ ông đời ' . $n,
            }))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->vi(match (true) {
                $n === 3 => 'cụ',
                $n === 4 => 'kỵ',
                default  => 'tổ tiên đời ' . $n,
            }))->ancestor(),
            // Dynamic: descendants — chắt (great), chút (great-great), chít (beyond)
            Relationship::dynamic(fn (int $n) => $this->vi(match (true) {
                $n === 3 => 'chắt gái',
                $n === 4 => 'chút gái',
                default  => 'chít gái đời ' . $n,
            }))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->vi(match (true) {
                $n === 3 => 'chắt trai',
                $n === 4 => 'chút trai',
                default  => 'chít trai đời ' . $n,
            }))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->vi(match (true) {
                $n === 3 => 'chắt',
                $n === 4 => 'chút',
                default  => 'chít đời ' . $n,
            }))->descendant(),
        ];
    }
}
