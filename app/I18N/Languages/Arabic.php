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

use Fisharebest\ExtCalendar\ArabicCalendar;
use Fisharebest\ExtCalendar\CalendarInterface;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Enums\Script;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

final readonly class Arabic extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::SixFormsArabic;

    protected const string    ENDONYM            = 'العربية';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'ar';
    protected const string    LOCALE_CODE = 'ar_001@collation=phonebook';
    protected const array     DIGITS      = [
        0   => UTF8::ARABIC_INDIC_DIGIT_ZERO,
        1   => UTF8::ARABIC_INDIC_DIGIT_ONE,
        2   => UTF8::ARABIC_INDIC_DIGIT_TWO,
        3   => UTF8::ARABIC_INDIC_DIGIT_THREE,
        4   => UTF8::ARABIC_INDIC_DIGIT_FOUR,
        5   => UTF8::ARABIC_INDIC_DIGIT_FIVE,
        6   => UTF8::ARABIC_INDIC_DIGIT_SIX,
        7   => UTF8::ARABIC_INDIC_DIGIT_SEVEN,
        8   => UTF8::ARABIC_INDIC_DIGIT_EIGHT,
        9   => UTF8::ARABIC_INDIC_DIGIT_NINE,
    ];
    protected const string    DIGITS_SEPARATOR   = UTF8::ARABIC_THOUSANDS_SEPARATOR;
    protected const string    NEGATIVE_SYMBOL    = UTF8::ARABIC_LETTER_MARK . '-';
    protected const string    DECIMAL_SYMBOL     = UTF8::ARABIC_DECIMAL_SEPARATOR;
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::ARABIC_PERCENT_SIGN . UTF8::ARABIC_LETTER_MARK;
    protected const Script    SCRIPT             = Script::Arab;
    protected const string    DATE_ABOUT         = 'حوالي %s';
    protected const string    DATE_AFTER         = 'بعد %s';
    protected const string    DATE_BEFORE        = 'قبل %s';
    protected const string    DATE_BETWEEN_AND   = 'بين %s و %s';
    protected const string    DATE_CALCULATED    = 'حسب %s';
    protected const string    DATE_ESTIMATED     = 'تقديراً %s';
    protected const string    DATE_FROM          = 'من %s';
    protected const string    DATE_FROM_TO       = 'من %s إلى %s';
    protected const string    DATE_INTERPRETED   = 'أعتبر %s';
    protected const string    DATE_TO            = 'إلى %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'ق.م';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'م';
    protected const string    LIST_SEPARATOR     = '، ';
    protected const string    LIST_SEPARATOR_AND = ' و';
    protected const string    LIST_SEPARATOR_OR  = ' أو';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'يناير',
        'فبراير',
        'مارس',
        'أبريل',
        'مايو',
        'يونيو',
        'يوليو',
        'أغسطس',
        'سبتمبر',
        'أكتوبر',
        'نوفمبر',
        'ديسمبر',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'تشرين',
        'حِشوان',
        'كِسلو',
        'طِيبيت',
        'شباط',
        'أدار الأول',
        'أدار الثاني',
        'أدار',
        'نيسان',
        'إيار',
        'سيوان',
        'تموز',
        'آب',
        'إيلول',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'فاندميير',
        'برومير',
        'فريمير',
        'نيفوا',
        'بلوفوا',
        'فينتوا',
        'جيرمينال',
        'فلوريال',
        'براريال',
        'ميسيدور',
        'ثيرميدور',
        'فركتيدور',
        'أيام مكملة',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'محرّم',
        'صفر',
        'ربيع الأول',
        'ربيع الثاني',
        'جمادى الأول',
        'جمادى الثاني',
        'رجب',
        'شعبان',
        'رمضان',
        'شوّال',
        'ذو القعدة',
        'ذو الحجة',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'فروردين',
        'ارديبهشت',
        'خُرداد',
        'تير',
        'مُرداد',
        'شهريور',
        'مِهر',
        'آبان',
        'آذر',
        'دى',
        'بهمن',
        'إسفند',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    /** @var array<int,string> */
    protected const array ALPHABET = [
        UTF8::ARABIC_LETTER_ALEF,
        UTF8::ARABIC_LETTER_BEH,
        UTF8::ARABIC_LETTER_TEH,
        UTF8::ARABIC_LETTER_THEH,
        UTF8::ARABIC_LETTER_JEEM,
        UTF8::ARABIC_LETTER_HAH,
        UTF8::ARABIC_LETTER_KHAH,
        UTF8::ARABIC_LETTER_DAL,
        UTF8::ARABIC_LETTER_THAL,
        UTF8::ARABIC_LETTER_REH,
        UTF8::ARABIC_LETTER_ZAIN,
        UTF8::ARABIC_LETTER_SEEN,
        UTF8::ARABIC_LETTER_SHEEN,
        UTF8::ARABIC_LETTER_SAD,
        UTF8::ARABIC_LETTER_DAD,
        UTF8::ARABIC_LETTER_TAH,
        UTF8::ARABIC_LETTER_ZAH,
        UTF8::ARABIC_LETTER_AIN,
        UTF8::ARABIC_LETTER_GHAIN,
        UTF8::ARABIC_LETTER_FEH,
        UTF8::ARABIC_LETTER_QAF,
        UTF8::ARABIC_LETTER_KAF,
        UTF8::ARABIC_LETTER_LAM,
        UTF8::ARABIC_LETTER_MEEM,
        UTF8::ARABIC_LETTER_NOON,
        UTF8::ARABIC_LETTER_HEH,
        UTF8::ARABIC_LETTER_WAW,
        UTF8::ARABIC_LETTER_YEH,
        UTF8::ARABIC_LETTER_HAMZA,
        UTF8::ARABIC_LETTER_TEH_MARBUTA,
        UTF8::ARABIC_LETTER_ALEF_MAKSURA,
        UTF8::ARABIC_LETTER_WAW,
    ];

    public function calendar(): CalendarInterface
    {
        return new ArabicCalendar();
    }

    /**
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        // Issue #5262 - the INTL library doesn't convert these.
        return [
            UTF8::ARABIC_LETTER_TEH_MARBUTA  => UTF8::ARABIC_LETTER_TEH,
            UTF8::ARABIC_LETTER_ALEF_MAKSURA => UTF8::ARABIC_LETTER_YEH,
            UTF8::ARABIC_LETTER_ALEF_WASLA   => UTF8::ARABIC_LETTER_ALEF,
        ];
    }

    /**
     * @return array<Relationship>
     */
        /** @return array{string, string} */
    private function ar(string $s): array
    {
        return [$s, '%s ' . $s];
    }

    /** @return array{string, string} */
    private function ar2(string $nom, string $gen): array
    {
        return [$nom, '%s ' . $gen];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->ar('أم بالتبني'))->adoptive()->mother(),
            Relationship::fixed(...$this->ar('أب بالتبني'))->adoptive()->father(),
            Relationship::fixed(...$this->ar('والد/ة بالتبني'))->adoptive()->parent(),
            Relationship::fixed(...$this->ar('ابنة بالتبني'))->adopted()->daughter(),
            Relationship::fixed(...$this->ar('ابن بالتبني'))->adopted()->son(),
            Relationship::fixed(...$this->ar('طفل بالتبني'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->ar('أم حاضنة'))->fostering()->mother(),
            Relationship::fixed(...$this->ar('أب حاضن'))->fostering()->father(),
            Relationship::fixed(...$this->ar('والد/ة حاضن/ة'))->fostering()->parent(),
            Relationship::fixed(...$this->ar('ابنة بالحضانة'))->fostered()->daughter(),
            Relationship::fixed(...$this->ar('ابن بالحضانة'))->fostered()->son(),
            Relationship::fixed(...$this->ar('طفل بالحضانة'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->ar('أم'))->mother(),
            Relationship::fixed(...$this->ar('أب'))->father(),
            Relationship::fixed(...$this->ar('والد'))->parent(),
            // Children
            Relationship::fixed(...$this->ar('ابنة'))->daughter(),
            Relationship::fixed(...$this->ar('ابن'))->son(),
            Relationship::fixed(...$this->ar('ولد'))->child(),
            // Siblings
            Relationship::fixed(...$this->ar('أخت توأم'))->multiple()->sister(),
            Relationship::fixed(...$this->ar('أخ توأم'))->multiple()->brother(),
            Relationship::fixed(...$this->ar('توأم'))->multiple()->sibling(),
            Relationship::fixed(...$this->ar('أخت كبرى'))->older()->sister(),
            Relationship::fixed(...$this->ar('أخ أكبر'))->older()->brother(),
            Relationship::fixed(...$this->ar('أخت صغرى'))->younger()->sister(),
            Relationship::fixed(...$this->ar('أخ أصغر'))->younger()->brother(),
            Relationship::fixed(...$this->ar('أخت'))->sister(),
            Relationship::fixed(...$this->ar('أخ'))->brother(),
            Relationship::fixed(...$this->ar('أخ/أخت'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$this->ar('أخت لأب'))->father()->daughter(),
            Relationship::fixed(...$this->ar('أخ لأب'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$this->ar('أخت لأم'))->mother()->daughter(),
            Relationship::fixed(...$this->ar('أخ لأم'))->mother()->son(),
            // Half-siblings (generic fallback)
            Relationship::fixed(...$this->ar('أخت غير شقيقة'))->parent()->daughter(),
            Relationship::fixed(...$this->ar('أخ غير شقيق'))->parent()->son(),
            Relationship::fixed(...$this->ar('أخ/أخت'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->ar2('زوجة الأب', 'زوجة أب'))->parent()->wife(),
            Relationship::fixed(...$this->ar2('زوج الأم', 'زوج أم'))->parent()->husband(),
            Relationship::fixed(...$this->ar('ربيبة'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->ar('ربيب'))->married()->spouse()->son(),
            Relationship::fixed(...$this->ar('ربيب/ربيبة'))->married()->spouse()->child(),
            Relationship::fixed(...$this->ar('ربيبة'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$this->ar('ربيب'))->parent()->spouse()->son(),
            Relationship::fixed(...$this->ar('ربيب/ربيبة'))->parent()->spouse()->child(),
            // Co-wives (ضرائر) — two wives married to the same husband
            Relationship::fixed(...$this->ar('ضرّة'))->husband()->wife(),
            // Partners
            Relationship::fixed(...$this->ar('مطلّقة'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->ar('مطلّق'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->ar('طليق/ة'))->divorced()->partner(),
            Relationship::fixed(...$this->ar('خطيبة'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->ar('خطيب'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->ar('زوجة'))->wife(),
            Relationship::fixed(...$this->ar('زوج'))->husband(),
            Relationship::fixed(...$this->ar('زوج/زوجة'))->spouse(),
            Relationship::fixed(...$this->ar('شريكة'))->partner()->female(),
            Relationship::fixed(...$this->ar('شريك'))->partner()->male(),
            Relationship::fixed(...$this->ar('شريك/شريكة'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$this->ar('حماة'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->ar('حمو'))->married()->spouse()->father(),
            Relationship::fixed(...$this->ar('والد/ة الزوج/ة'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$this->ar('كنّة'))->child()->wife(),
            Relationship::fixed(...$this->ar('صهر'))->child()->husband(),
            Relationship::fixed(...$this->ar('كنّة/صهر'))->child()->married()->spouse(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$this->ar('سلفة'))->spouse()->sister(),
            Relationship::fixed(...$this->ar('سلف'))->spouse()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$this->ar2('زوجة الأخ', 'زوجة أخ'))->sibling()->wife(),
            Relationship::fixed(...$this->ar2('زوج الأخت', 'زوج أخت'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->ar2('الجدة', 'جدة'))->parent()->mother(),
            Relationship::fixed(...$this->ar2('الجد', 'جد'))->parent()->father(),
            Relationship::fixed(...$this->ar('جد/جدة'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->ar('حفيدة'))->child()->daughter(),
            Relationship::fixed(...$this->ar('حفيد'))->child()->son(),
            Relationship::fixed(...$this->ar('حفيد/حفيدة'))->child()->child(),
            // Aunts — paternal/maternal distinction
            Relationship::fixed(...$this->ar('عمة'))->father()->sister(),
            Relationship::fixed(...$this->ar('خالة'))->mother()->sister(),
            Relationship::fixed(...$this->ar('عمة/خالة'))->parent()->sister(),
            // Uncles — paternal/maternal distinction
            Relationship::fixed(...$this->ar('عم'))->father()->brother(),
            Relationship::fixed(...$this->ar('خال'))->mother()->brother(),
            Relationship::fixed(...$this->ar('عم/خال'))->parent()->brother(),
            // Nieces — by brother or sister
            Relationship::fixed(...$this->ar2('بنت الأخ', 'بنت أخ'))->brother()->daughter(),
            Relationship::fixed(...$this->ar2('بنت الأخت', 'بنت أخت'))->sister()->daughter(),
            Relationship::fixed(...$this->ar('بنت الأخ/الأخت'))->sibling()->daughter(),
            // Nephews — by brother or sister
            Relationship::fixed(...$this->ar2('ابن الأخ', 'ابن أخ'))->brother()->son(),
            Relationship::fixed(...$this->ar2('ابن الأخت', 'ابن أخت'))->sister()->son(),
            Relationship::fixed(...$this->ar('ابن الأخ/الأخت'))->sibling()->son(),
            // Nieces/nephews via in-laws
            Relationship::fixed(...$this->ar('بنت الأخ/الأخت'))->married()->spouse()->sibling()->daughter(),
            Relationship::fixed(...$this->ar('ابن الأخ/الأخت'))->married()->spouse()->sibling()->son(),
            // Cousins — paternal uncle's children
            Relationship::fixed(...$this->ar2('بنت العم', 'بنت عم'))->father()->brother()->daughter(),
            Relationship::fixed(...$this->ar2('ابن العم', 'ابن عم'))->father()->brother()->son(),
            // Cousins — paternal aunt's children
            Relationship::fixed(...$this->ar2('بنت العمة', 'بنت عمة'))->father()->sister()->daughter(),
            Relationship::fixed(...$this->ar2('ابن العمة', 'ابن عمة'))->father()->sister()->son(),
            // Cousins — maternal uncle's children
            Relationship::fixed(...$this->ar2('بنت الخال', 'بنت خال'))->mother()->brother()->daughter(),
            Relationship::fixed(...$this->ar2('ابن الخال', 'ابن خال'))->mother()->brother()->son(),
            // Cousins — maternal aunt's children
            Relationship::fixed(...$this->ar2('بنت الخالة', 'بنت خالة'))->mother()->sister()->daughter(),
            Relationship::fixed(...$this->ar2('ابن الخالة', 'ابن خالة'))->mother()->sister()->son(),
            // Generic cousin fallback
            Relationship::fixed(...$this->ar('ابنة عم/خال'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->ar('ابن عم/خال'))->parent()->sibling()->son(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->ar('عمة/خالة كبرى' . ($n > 2 ? ' ×' . ($n - 1) : '')))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->ar('عم/خال أكبر' . ($n > 2 ? ' ×' . ($n - 1) : '')))->ancestor()->brother(),
            // Dynamic: grand-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->ar('بنت أخ/أخت كبرى' . ($n > 2 ? ' ×' . ($n - 1) : '')))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->ar('بنت أخ/أخت كبرى' . ($n > 2 ? ' ×' . ($n - 1) : '')))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->ar('ابن أخ/أخت أكبر' . ($n > 2 ? ' ×' . ($n - 1) : '')))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->ar('ابن أخ/أخت أكبر' . ($n > 2 ? ' ×' . ($n - 1) : '')))->married()->spouse()->sibling()->descendant()->male(),
            // Dynamic: ancestors
            Relationship::dynamic(fn (int $n) => $this->ar2('الجدة الكبرى' . ($n > 3 ? ' ×' . ($n - 2) : ''), 'جدة كبرى' . ($n > 3 ? ' ×' . ($n - 2) : '')))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->ar2('الجد الأكبر' . ($n > 3 ? ' ×' . ($n - 2) : ''), 'جد أكبر' . ($n > 3 ? ' ×' . ($n - 2) : '')))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->ar('جد/جدة أكبر' . ($n > 3 ? ' ×' . ($n - 2) : '')))->ancestor(),
            // Dynamic: descendants
            Relationship::dynamic(fn (int $n) => $this->ar('حفيدة كبرى' . ($n > 3 ? ' ×' . ($n - 2) : '')))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->ar('حفيد أكبر' . ($n > 3 ? ' ×' . ($n - 2) : '')))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->ar('حفيد/حفيدة' . ($n > 3 ? ' ×' . ($n - 2) : '')))->descendant(),
        ];
    }
}
