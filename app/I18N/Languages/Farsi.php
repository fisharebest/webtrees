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
use Fisharebest\Webtrees\Enums\Weekday;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

final readonly class Farsi extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'فارسی';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'fa';
    protected const string    LOCALE_CODE = 'fa_IR@collation=phonebook';
    protected const array     DIGITS      = [
        0   => UTF8::EXTENDED_ARABIC_INDIC_DIGIT_ZERO,
        1   => UTF8::EXTENDED_ARABIC_INDIC_DIGIT_ONE,
        2   => UTF8::EXTENDED_ARABIC_INDIC_DIGIT_TWO,
        3   => UTF8::EXTENDED_ARABIC_INDIC_DIGIT_THREE,
        4   => UTF8::EXTENDED_ARABIC_INDIC_DIGIT_FOUR,
        5   => UTF8::EXTENDED_ARABIC_INDIC_DIGIT_FIVE,
        6   => UTF8::EXTENDED_ARABIC_INDIC_DIGIT_SIX,
        7   => UTF8::EXTENDED_ARABIC_INDIC_DIGIT_SEVEN,
        8   => UTF8::EXTENDED_ARABIC_INDIC_DIGIT_EIGHT,
        9   => UTF8::EXTENDED_ARABIC_INDIC_DIGIT_NINE,
    ];
    protected const string    DIGITS_SEPARATOR   = UTF8::ARABIC_THOUSANDS_SEPARATOR;
    protected const string    NEGATIVE_SYMBOL    = UTF8::LEFT_TO_RIGHT_MARK . UTF8::MINUS_SIGN;
    protected const string    DECIMAL_SYMBOL     = UTF8::ARABIC_DECIMAL_SEPARATOR;
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::ARABIC_PERCENT_SIGN;
    protected const Script    SCRIPT             = Script::Arab;
    protected const Weekday   FIRST_DAY          = Weekday::Saturday;
    protected const string    DATE_ABOUT         = 'درباره %s';
    protected const string    DATE_AFTER         = 'بعد %s';
    protected const string    DATE_BEFORE        = 'قبل %s';
    protected const string    DATE_BETWEEN_AND   = 'بین %s و %s';
    protected const string    DATE_CALCULATED    = 'محاسبه شده %s';
    protected const string    DATE_ESTIMATED     = 'برآورد شده %s';
    protected const string    DATE_FROM          = 'از %s';
    protected const string    DATE_FROM_TO       = 'از %s تا %s';
    protected const string    DATE_INTERPRETED   = 'تعریف شده %s';
    protected const string    DATE_TO            = 'به %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'قبل ازمیلاد';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'پس ازمیلاد';
    protected const string    LIST_SEPARATOR     = '، ';
    protected const string    LIST_SEPARATOR_AND = ' و ';
    protected const string    LIST_SEPARATOR_OR  = ' یا ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'ژانویه',
        'فوریه',
        'مارس',
        'آوریل',
        'می',
        'ژوئن',
        'جولای',
        'آگوست',
        'سپتامبر',
        'اکتبر',
        'نوامبر',
        'دسامبر',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'تیشری',
        'هشوان',
        'کیسلِو',
        'تِوِت',
        'شوات',
        'ادار ۱',
        'ادار ۲',
        'ادار',
        'نیسان',
        'لیار',
        'سیوان',
        'تموز',
        'آو',
        'اِلول',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'وندیمیر',
        'برومیر',
        'فریمایر',
        'نیوسه',
        'پلویوسه',
        'ونتوسه',
        'ژرمینال',
        'فلورل',
        'پریریال',
        'مسیدور',
        'ترمیدور',
        'فروکتیدور',
        'جورس کومپلمنتایرس',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'محرم',
        'صفر',
        'ربیع الاول',
        'ربیع الثانی',
        'جمادی الاول',
        'جمادی الثانی',
        'رجب',
        'شعبان',
        'رمضان',
        'شوال',
        'ذوالقعده',
        'ذوالحجه',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'فروردین',
        'اردیبهشت',
        'خرداد',
        'تیر',
        'مرداد',
        'شهریور',
        'مهر',
        'آبان',
        'آذر',
        'دی',
        'بهمن',
        'اسفند',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;
    /**
     * @return array<int,string>
     */
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
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Farsi ezafe genitive: "%s term" — possessed comes before possessor
        $fa = static fn (string $s): array => [$s, '%s ' . $s];

        return [
            // Adopted
            Relationship::fixed(...$fa('مادرخوانده'))->adoptive()->mother(),
            Relationship::fixed(...$fa('پدرخوانده'))->adoptive()->father(),
            Relationship::fixed(...$fa('والد خوانده'))->adoptive()->parent(),
            Relationship::fixed(...$fa('دخترخوانده'))->adopted()->daughter(),
            Relationship::fixed(...$fa('پسرخوانده'))->adopted()->son(),
            Relationship::fixed(...$fa('فرزندخوانده'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$fa('مادر رضاعی'))->fostering()->mother(),
            Relationship::fixed(...$fa('پدر رضاعی'))->fostering()->father(),
            Relationship::fixed(...$fa('والد رضاعی'))->fostering()->parent(),
            Relationship::fixed(...$fa('دختر رضاعی'))->fostered()->daughter(),
            Relationship::fixed(...$fa('پسر رضاعی'))->fostered()->son(),
            Relationship::fixed(...$fa('فرزند رضاعی'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$fa('مادر'))->mother(),
            Relationship::fixed(...$fa('پدر'))->father(),
            Relationship::fixed(...$fa('والد'))->parent(),
            // Children
            Relationship::fixed(...$fa('دختر'))->daughter(),
            Relationship::fixed(...$fa('پسر'))->son(),
            Relationship::fixed(...$fa('فرزند'))->child(),
            // Siblings
            Relationship::fixed(...$fa('خواهر دوقلو'))->twin()->sister(),
            Relationship::fixed(...$fa('برادر دوقلو'))->twin()->brother(),
            Relationship::fixed(...$fa('دوقلو'))->twin()->sibling(),
            Relationship::fixed(...$fa('خواهر بزرگ‌تر'))->older()->sister(),
            Relationship::fixed(...$fa('برادر بزرگ‌تر'))->older()->brother(),
            Relationship::fixed(...$fa('خواهر کوچک‌تر'))->younger()->sister(),
            Relationship::fixed(...$fa('برادر کوچک‌تر'))->younger()->brother(),
            Relationship::fixed(...$fa('خواهر'))->sister(),
            Relationship::fixed(...$fa('برادر'))->brother(),
            Relationship::fixed(...$fa('خواهر/برادر'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$fa('خواهر پدری'))->father()->daughter(),
            Relationship::fixed(...$fa('برادر پدری'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$fa('خواهر مادری'))->mother()->daughter(),
            Relationship::fixed(...$fa('برادر مادری'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$fa('خواهر ناتنی'))->parent()->daughter(),
            Relationship::fixed(...$fa('برادر ناتنی'))->parent()->son(),
            Relationship::fixed(...$fa('خواهر/برادر'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$fa('نامادری'))->parent()->wife(),
            Relationship::fixed(...$fa('ناپدری'))->parent()->husband(),
            Relationship::fixed(...$fa('نادختری'))->married()->spouse()->daughter(),
            Relationship::fixed(...$fa('ناپسری'))->married()->spouse()->son(),
            Relationship::fixed(...$fa('نافرزندی'))->married()->spouse()->child(),
            Relationship::fixed(...$fa('نادختری'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$fa('ناپسری'))->parent()->spouse()->son(),
            Relationship::fixed(...$fa('نافرزندی'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$fa('همسر سابق'))->divorced()->partner()->female(),
            Relationship::fixed(...$fa('شوهر سابق'))->divorced()->partner()->male(),
            Relationship::fixed(...$fa('همسر سابق'))->divorced()->partner(),
            Relationship::fixed(...$fa('نامزد'))->engaged()->partner()->female(),
            Relationship::fixed(...$fa('نامزد'))->engaged()->partner()->male(),
            Relationship::fixed(...$fa('زن'))->wife(),
            Relationship::fixed(...$fa('شوهر'))->husband(),
            Relationship::fixed(...$fa('همسر'))->spouse(),
            Relationship::fixed(...$fa('شریک'))->partner(),
            // In-laws (spouse's parents) — Farsi distinguishes husband's vs wife's side
            Relationship::fixed(...$fa('مادرشوهر'))->husband()->mother(),
            Relationship::fixed(...$fa('پدرشوهر'))->husband()->father(),
            Relationship::fixed(...$fa('مادرزن'))->wife()->mother(),
            Relationship::fixed(...$fa('پدرزن'))->wife()->father(),
            Relationship::fixed(...$fa('والد همسر'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$fa('عروس'))->child()->wife(),
            Relationship::fixed(...$fa('داماد'))->child()->husband(),
            Relationship::fixed(...$fa('عروس/داماد'))->child()->married()->spouse(),
            // In-laws (spouse's siblings) — distinguished by side
            Relationship::fixed(...$fa('خواهرشوهر'))->husband()->sister(),
            Relationship::fixed(...$fa('برادرشوهر'))->husband()->brother(),
            Relationship::fixed(...$fa('خواهرزن'))->wife()->sister(),
            Relationship::fixed(...$fa('برادرزن'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$fa('زن برادر'))->brother()->wife(),
            Relationship::fixed(...$fa('شوهر خواهر'))->sister()->husband(),
            Relationship::fixed(...$fa('همسر خواهر/برادر'))->sibling()->spouse(),
            // Grandparents
            Relationship::fixed(...$fa('مادربزرگ'))->parent()->mother(),
            Relationship::fixed(...$fa('پدربزرگ'))->parent()->father(),
            Relationship::fixed(...$fa('پدربزرگ/مادربزرگ'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$fa('نوه'))->child()->daughter(),
            Relationship::fixed(...$fa('نوه'))->child()->son(),
            Relationship::fixed(...$fa('نوه'))->child()->child(),
            // Aunts — paternal/maternal
            Relationship::fixed(...$fa('عمه'))->father()->sister(),
            Relationship::fixed(...$fa('خاله'))->mother()->sister(),
            Relationship::fixed(...$fa('عمه/خاله'))->parent()->sister(),
            // Uncles — paternal/maternal
            Relationship::fixed(...$fa('عمو'))->father()->brother(),
            Relationship::fixed(...$fa('دایی'))->mother()->brother(),
            Relationship::fixed(...$fa('عمو/دایی'))->parent()->brother(),
            // Nieces/Nephews — through brother
            Relationship::fixed(...$fa('دختر برادر'))->brother()->daughter(),
            Relationship::fixed(...$fa('پسر برادر'))->brother()->son(),
            // Nieces/Nephews — through sister
            Relationship::fixed(...$fa('دختر خواهر'))->sister()->daughter(),
            Relationship::fixed(...$fa('پسر خواهر'))->sister()->son(),
            // Generic niece/nephew
            Relationship::fixed(...$fa('خواهرزاده/برادرزاده'))->sibling()->daughter(),
            Relationship::fixed(...$fa('خواهرزاده/برادرزاده'))->sibling()->son(),
            // Cousins — paternal uncle's children
            Relationship::fixed(...$fa('دخترعمو'))->father()->brother()->daughter(),
            Relationship::fixed(...$fa('پسرعمو'))->father()->brother()->son(),
            // Cousins — paternal aunt's children
            Relationship::fixed(...$fa('دخترعمه'))->father()->sister()->daughter(),
            Relationship::fixed(...$fa('پسرعمه'))->father()->sister()->son(),
            // Cousins — maternal uncle's children
            Relationship::fixed(...$fa('دختردایی'))->mother()->brother()->daughter(),
            Relationship::fixed(...$fa('پسردایی'))->mother()->brother()->son(),
            // Cousins — maternal aunt's children
            Relationship::fixed(...$fa('دخترخاله'))->mother()->sister()->daughter(),
            Relationship::fixed(...$fa('پسرخاله'))->mother()->sister()->son(),
            // Generic cousin fallback
            Relationship::fixed(...$fa('دختر عمو/دایی'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$fa('پسر عمو/دایی'))->parent()->sibling()->son(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $fa('عمه/خاله بزرگ' . ($n > 2 ? ' ×' . ($n - 1) : '')))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $fa('عمو/دایی بزرگ' . ($n > 2 ? ' ×' . ($n - 1) : '')))->ancestor()->brother(),
            // Dynamic: grand-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $fa('خواهرزاده/برادرزاده بزرگ' . ($n > 2 ? ' ×' . ($n - 1) : '')))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $fa('خواهرزاده/برادرزاده بزرگ' . ($n > 2 ? ' ×' . ($n - 1) : '')))->married()->spouse()->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $fa('خواهرزاده/برادرزاده بزرگ' . ($n > 2 ? ' ×' . ($n - 1) : '')))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $fa('خواهرزاده/برادرزاده بزرگ' . ($n > 2 ? ' ×' . ($n - 1) : '')))->married()->spouse()->sibling()->descendant()->male(),
            // Dynamic: ancestors
            Relationship::dynamic(static fn (int $n) => $fa('مادربزرگ بزرگ' . ($n > 3 ? ' ×' . ($n - 2) : '')))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $fa('پدربزرگ بزرگ' . ($n > 3 ? ' ×' . ($n - 2) : '')))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $fa('نیا بزرگ' . ($n > 3 ? ' ×' . ($n - 2) : '')))->ancestor(),
            // Dynamic: descendants
            Relationship::dynamic(static fn (int $n) => $fa('نتیجه' . ($n > 3 ? ' ×' . ($n - 2) : '')))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $fa('نتیجه' . ($n > 3 ? ' ×' . ($n - 2) : '')))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $fa('نتیجه' . ($n > 3 ? ' ×' . ($n - 2) : '')))->descendant(),
        ];
    }
}
