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

final readonly class Urdu extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'اردو';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'ur';
    protected const string    LOCALE_CODE        = 'ur_PK@collation=phonebook';
    protected const int       DIGITS_GROUP = 2;
    protected const array     DIGITS       = [
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
    protected const string    DIGITS_SEPARATOR   = parent::DIGITS_SEPARATOR;
    protected const string    NEGATIVE_SYMBOL    = UTF8::LEFT_TO_RIGHT_MARK . '-';
    protected const string    DECIMAL_SYMBOL     = parent::DECIMAL_SYMBOL;
    protected const Script    SCRIPT             = Script::Arab;
    protected const Weekday   FIRST_DAY          = Weekday::Sunday;
    protected const string    DATE_ABOUT         = 'تقریباً %s';
    protected const string    DATE_AFTER         = 'بعد از %s';
    protected const string    DATE_BEFORE        = 'قبل از %s';
    protected const string    DATE_BETWEEN_AND   = '%s اور %s کے درمیان';
    protected const string    DATE_CALCULATED    = 'بالحساب %s';
    protected const string    DATE_ESTIMATED     = 'اندازاً %s';
    protected const string    DATE_FROM          = '%s سے';
    protected const string    DATE_FROM_TO       = '%s سے %s تک';
    protected const string    DATE_INTERPRETED   = 'تشریحی %s';
    protected const string    DATE_TO            = '%s تک';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'قبل مسیح';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'عیسوی';
    protected const string    LIST_SEPARATOR     = '، ';
    protected const string    LIST_SEPARATOR_AND = ' اور ';
    protected const string    LIST_SEPARATOR_OR  = ' یا ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'جنوری',
        'فروری',
        'مارچ',
        'اپریل',
        'مئی',
        'جون',
        'جولائی',
        'اگست',
        'ستمبر',
        'اکتوبر',
        'نومبر',
        'دسمبر',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'تشیری',
        'ہشیوان',
        'کِیسلو',
        'تیوت',
        'شوات',
        'ادار اول',
        'ادار دوم',
        'ادار',
        'نسان',
        'ایار',
        'سیوان',
        'تموز',
        'آو',
        'ایلول',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'وینڈیمیئر',
        'بروومیر',
        'فریمیر',
        'نیووز',
        'پلویوز',
        'وینٹوز',
        'جرمنل',
        'فلوریل',
        'پریریئل',
        'میسڈر',
        'تھرمائڈور',
        'فروٹڈور',
        'جورس کمپلیمینٹریس',
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
        'ذوالقعدہ',
        'ذوالحج',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'فروردین',
        'اردی بہشت',
        'خرداد',
        'تیر',
        'مرداد',
        'شہریور',
        'مہر',
        'آبان',
        'آذر',
        'دے',
        'بہمن',
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
        'ا',
        'ب',
        'ت',
        'ث',
        'ج',
        'ح',
        'خ',
        'د',
        'ذ',
        'ر',
        'ز',
        'س',
        'ش',
        'ص',
        'ض',
        'ط',
        'ظ',
        'ع',
        'غ',
        'ف',
        'ق',
        'ك',
        'ل',
        'م',
        'ن',
        'ه',
        'و',
        'ي',
        'آ',
        'ة',
        'ى',
        'ی',
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
        // Urdu genitive: postposition کا (ka, male possessed) / کی (ki, female possessed)
        $ur = static fn (string $s): array => [$s, $s . ' کا %s', $s . ' کی %s'];

        return [
            // Adopted
            Relationship::fixed(...$ur('سوتیلی امّی'))->adoptive()->mother(),
            Relationship::fixed(...$ur('سوتیلا ابّو'))->adoptive()->father(),
            Relationship::fixed(...$ur('سوتیلا والدین'))->adoptive()->parent(),
            Relationship::fixed(...$ur('لے پالک بیٹی'))->adopted()->daughter(),
            Relationship::fixed(...$ur('لے پالک بیٹا'))->adopted()->son(),
            Relationship::fixed(...$ur('لے پالک بچّہ'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$ur('رضاعی امّی'))->fostering()->mother(),
            Relationship::fixed(...$ur('رضاعی ابّو'))->fostering()->father(),
            Relationship::fixed(...$ur('رضاعی والدین'))->fostering()->parent(),
            Relationship::fixed(...$ur('رضاعی بیٹی'))->fostered()->daughter(),
            Relationship::fixed(...$ur('رضاعی بیٹا'))->fostered()->son(),
            Relationship::fixed(...$ur('رضاعی بچّہ'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$ur('امّی'))->mother(),
            Relationship::fixed(...$ur('ابّو'))->father(),
            Relationship::fixed(...$ur('والدین'))->parent(),
            // Children
            Relationship::fixed(...$ur('بیٹی'))->daughter(),
            Relationship::fixed(...$ur('بیٹا'))->son(),
            Relationship::fixed(...$ur('بچّہ'))->child(),
            // Siblings — twins
            Relationship::fixed(...$ur('جڑواں بہن'))->twin()->sister(),
            Relationship::fixed(...$ur('جڑواں بھائی'))->twin()->brother(),
            Relationship::fixed(...$ur('جڑواں'))->twin()->sibling(),
            // Siblings — elder/younger
            Relationship::fixed(...$ur('بڑی بہن'))->older()->sister(),
            Relationship::fixed(...$ur('چھوٹی بہن'))->younger()->sister(),
            Relationship::fixed(...$ur('بڑا بھائی'))->older()->brother(),
            Relationship::fixed(...$ur('چھوٹا بھائی'))->younger()->brother(),
            Relationship::fixed(...$ur('بہن'))->sister(),
            Relationship::fixed(...$ur('بھائی'))->brother(),
            Relationship::fixed(...$ur('بہن بھائی'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$ur('سوتیلی بہن'))->father()->daughter(),
            Relationship::fixed(...$ur('سوتیلا بھائی'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$ur('سوتیلی بہن'))->mother()->daughter(),
            Relationship::fixed(...$ur('سوتیلا بھائی'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$ur('سوتیلی بہن'))->parent()->daughter(),
            Relationship::fixed(...$ur('سوتیلا بھائی'))->parent()->son(),
            Relationship::fixed(...$ur('بہن بھائی'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$ur('سوتیلی امّی'))->parent()->wife(),
            Relationship::fixed(...$ur('سوتیلا ابّو'))->parent()->husband(),
            Relationship::fixed(...$ur('سوتیلی بیٹی'))->married()->spouse()->daughter(),
            Relationship::fixed(...$ur('سوتیلا بیٹا'))->married()->spouse()->son(),
            Relationship::fixed(...$ur('سوتیلا بچّہ'))->married()->spouse()->child(),
            Relationship::fixed(...$ur('سوتیلی بیٹی'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$ur('سوتیلا بیٹا'))->parent()->spouse()->son(),
            Relationship::fixed(...$ur('سوتیلا بچّہ'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$ur('سابقہ بیوی'))->divorced()->partner()->female(),
            Relationship::fixed(...$ur('سابقہ شوہر'))->divorced()->partner()->male(),
            Relationship::fixed(...$ur('سابقہ شریکِ حیات'))->divorced()->partner(),
            Relationship::fixed(...$ur('منگیتر'))->engaged()->partner()->female(),
            Relationship::fixed(...$ur('منگیتر'))->engaged()->partner()->male(),
            Relationship::fixed(...$ur('بیوی'))->wife(),
            Relationship::fixed(...$ur('شوہر'))->husband(),
            Relationship::fixed(...$ur('شریکِ حیات'))->spouse(),
            Relationship::fixed(...$ur('ساتھی'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$ur('ساس'))->married()->spouse()->mother(),
            Relationship::fixed(...$ur('سسر'))->married()->spouse()->father(),
            Relationship::fixed(...$ur('سسرال'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$ur('بہو'))->child()->wife(),
            Relationship::fixed(...$ur('داماد'))->child()->husband(),
            Relationship::fixed(...$ur('بہو/داماد'))->child()->married()->spouse(),
            // In-laws (husband's siblings)
            Relationship::fixed(...$ur('ننّد'))->husband()->sister(),
            Relationship::fixed(...$ur('جیٹھ'))->husband()->older()->brother(),
            Relationship::fixed(...$ur('دیور'))->husband()->younger()->brother(),
            Relationship::fixed(...$ur('دیور/جیٹھ'))->husband()->brother(),
            // In-laws (wife's siblings)
            Relationship::fixed(...$ur('سالی'))->wife()->sister(),
            Relationship::fixed(...$ur('سالا'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$ur('بھابھی'))->brother()->wife(),
            Relationship::fixed(...$ur('بہنوئی'))->older()->sister()->husband(),
            Relationship::fixed(...$ur('بہنوئی'))->younger()->sister()->husband(),
            Relationship::fixed(...$ur('بہنوئی'))->sister()->husband(),
            // Grandparents — paternal
            Relationship::fixed(...$ur('دادی'))->father()->mother(),
            Relationship::fixed(...$ur('دادا'))->father()->father(),
            // Grandparents — maternal
            Relationship::fixed(...$ur('نانی'))->mother()->mother(),
            Relationship::fixed(...$ur('نانا'))->mother()->father(),
            // Grandparents — generic fallback
            Relationship::fixed(...$ur('دادی/نانی'))->parent()->mother(),
            Relationship::fixed(...$ur('دادا/نانا'))->parent()->father(),
            Relationship::fixed(...$ur('دادا دادی/نانا نانی'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$ur('پوتی'))->son()->daughter(),
            Relationship::fixed(...$ur('پوتا'))->son()->son(),
            Relationship::fixed(...$ur('نواسی'))->daughter()->daughter(),
            Relationship::fixed(...$ur('نواسا'))->daughter()->son(),
            Relationship::fixed(...$ur('پوتی/نواسی'))->child()->daughter(),
            Relationship::fixed(...$ur('پوتا/نواسا'))->child()->son(),
            Relationship::fixed(...$ur('پوتا پوتی'))->child()->child(),
            // Aunts — paternal
            Relationship::fixed(...$ur('پھوپھی'))->father()->sister(),
            // Aunts — maternal
            Relationship::fixed(...$ur('خالہ'))->mother()->sister(),
            // Aunts — generic
            Relationship::fixed(...$ur('پھوپھی/خالہ'))->parent()->sister(),
            // Uncles — paternal
            Relationship::fixed(...$ur('چچا'))->father()->brother(),
            // Uncles — maternal
            Relationship::fixed(...$ur('ماموں'))->mother()->brother(),
            // Uncles — generic
            Relationship::fixed(...$ur('چچا/ماموں'))->parent()->brother(),
            // Uncle's/aunt's spouse
            Relationship::fixed(...$ur('چچی'))->father()->brother()->wife(),
            Relationship::fixed(...$ur('پھوپھا'))->father()->sister()->husband(),
            Relationship::fixed(...$ur('ممانی'))->mother()->brother()->wife(),
            Relationship::fixed(...$ur('خالو'))->mother()->sister()->husband(),
            // Nieces/Nephews — through brother
            Relationship::fixed(...$ur('بھتیجی'))->brother()->daughter(),
            Relationship::fixed(...$ur('بھتیجا'))->brother()->son(),
            // Nieces/Nephews — through sister
            Relationship::fixed(...$ur('بھانجی'))->sister()->daughter(),
            Relationship::fixed(...$ur('بھانجا'))->sister()->son(),
            // Generic niece/nephew
            Relationship::fixed(...$ur('بھتیجی/بھانجی'))->sibling()->daughter(),
            Relationship::fixed(...$ur('بھتیجا/بھانجا'))->sibling()->son(),
            // Cousins — paternal uncle's children (چچا زاد)
            Relationship::fixed(...$ur('چچا زاد بہن'))->father()->brother()->daughter(),
            Relationship::fixed(...$ur('چچا زاد بھائی'))->father()->brother()->son(),
            // Cousins — paternal aunt's children (پھوپھی زاد)
            Relationship::fixed(...$ur('پھوپھی زاد بہن'))->father()->sister()->daughter(),
            Relationship::fixed(...$ur('پھوپھی زاد بھائی'))->father()->sister()->son(),
            // Cousins — maternal uncle's children (ماموں زاد)
            Relationship::fixed(...$ur('ماموں زاد بہن'))->mother()->brother()->daughter(),
            Relationship::fixed(...$ur('ماموں زاد بھائی'))->mother()->brother()->son(),
            // Cousins — maternal aunt's children (خالہ زاد)
            Relationship::fixed(...$ur('خالہ زاد بہن'))->mother()->sister()->daughter(),
            Relationship::fixed(...$ur('خالہ زاد بھائی'))->mother()->sister()->son(),
            // Generic cousin fallback
            Relationship::fixed(...$ur('چچا زاد/ماموں زاد بہن'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$ur('چچا زاد/ماموں زاد بھائی'))->parent()->sibling()->son(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $ur('پھوپھی/خالہ' . ($n > 2 ? ' — نسل ' . ($n - 1) : ' بڑی')))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $ur('چچا/ماموں' . ($n > 2 ? ' — نسل ' . ($n - 1) : ' بڑے')))->ancestor()->brother(),
            // Dynamic: grand-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $ur('بھتیجی/بھانجی' . ($n > 2 ? ' — نسل ' . ($n - 1) : ' بڑی')))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $ur('بھتیجا/بھانجا' . ($n > 2 ? ' — نسل ' . ($n - 1) : ' بڑے')))->sibling()->descendant()->male(),
            // Dynamic: ancestors — great-grandparents (پر- prefix)
            Relationship::dynamic(static fn (int $n) => $ur(str_repeat('پر', $n - 2) . 'دادی'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $ur(str_repeat('پر', $n - 2) . 'دادا'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $ur(str_repeat('پر', $n - 2) . 'دادا/دادی'))->ancestor(),
            // Dynamic: descendants
            Relationship::dynamic(static fn (int $n) => $ur(str_repeat('پر', $n - 2) . 'پوتی'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $ur(str_repeat('پر', $n - 2) . 'پوتا'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $ur(str_repeat('پر', $n - 2) . 'پوتا پوتی'))->descendant(),
        ];
    }
}
