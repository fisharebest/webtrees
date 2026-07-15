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

use Fisharebest\ExtCalendar\CalendarInterface;
use Fisharebest\ExtCalendar\JewishCalendar;
use Fisharebest\Webtrees\Date\AbstractCalendarDate;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Enums\Script;
use Fisharebest\Webtrees\Enums\Weekday;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

final readonly class Hebrew extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'עברית';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'he';
    protected const string    LOCALE_CODE        = 'he_IL@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = parent::DIGITS_SEPARATOR;
    protected const string    NEGATIVE_SYMBOL    = UTF8::LEFT_TO_RIGHT_MARK . '-';
    protected const string    DECIMAL_SYMBOL     = parent::DECIMAL_SYMBOL;
    protected const Script    SCRIPT             = Script::Hebr;
    protected const Weekday   FIRST_DAY          = Weekday::Sunday;
    protected const string    DATE_ABOUT         = 'בערך %s';
    protected const string    DATE_AFTER         = 'אחרי %s';
    protected const string    DATE_BEFORE        = 'לפני %s';
    protected const string    DATE_BETWEEN_AND   = 'בין %s ל%s';
    protected const string    DATE_CALCULATED    = 'מחושב %s';
    protected const string    DATE_ESTIMATED     = 'מוערך %s';
    protected const string    DATE_FROM          = 'מ%s';
    protected const string    DATE_FROM_TO       = 'מ%s עד %s';
    protected const string    DATE_INTERPRETED   = 'פרשנות %s';
    protected const string    DATE_TO            = 'עד %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'לפנה״ס';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'אחה”ס';
    protected const string    LIST_SEPARATOR_AND = ' ו';
    protected const string    LIST_SEPARATOR_OR  = ' או';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'ינואר',
        'פברואר',
        'מרץ',
        'אפריל',
        'מאי',
        'יוני',
        'יולי',
        'אוגוסט',
        'ספטמבר',
        'אוקטובר',
        'נובמבר',
        'דצמבר',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'תשרי',
        'חשוון',
        'כסלו',
        'טבת',
        'שבט',
        'אדר א׳',
        'אדר ב׳',
        'אדר',
        'ניסן',
        'אייר',
        'סיוון',
        'תמוז',
        'אב',
        'אלול',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = [
        '',
        'בתשרי',
        'בחשוון',
        'בכסלו',
        'בטבת',
        'בשבט',
        'באדר א׳',
        'באדר ב׳',
        'באדר',
        'בניסן',
        'באייר',
        'בסיוון',
        'בתמוז',
        'באב',
        'באלול',
    ];

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'ונדמיר',
        'ברימר',
        'פרימר',
        'ניבוז',
        'פליביוז',
        'ונטוז',
        'ז׳רמינאל',
        'פלוראל',
        'פריריאל',
        'מסידור',
        'תרמידור',
        'פרוקטידור',
        'ימים משלימים',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'מוחרם',
        'צפר',
        'רביע אל-אוול',
        'רביע את-ת׳אני',
        'ג׳ומאדא אל-אוואל',
        'ג׳ומאדא אל-ת׳אניה',
        'רג׳ב',
        'שעבאן',
        'רמדאן',
        'שוואל',
        'ז׳ו אל-קעדה',
        'זו אל-חיג׳ה',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'פרברדין',
        'אורדיבהשת',
        'חורדאד',
        'טיר',
        'מורדאד',
        'שהריבר',
        'מהר',
        'אבאן',
        'אזר',
        'דיי',
        'בהמן',
        'אספנד',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;
    /**
     * @return array<int,string>
     */
    /** @var array<int,string> */
    protected const array ALPHABET = ['א', 'ב', 'ג', 'ד', 'ה', 'ו', 'ז', 'ח', 'ט', 'י', 'כ', 'ל', 'מ', 'נ', 'ס', 'ע', 'פ', 'צ', 'ק', 'ר', 'ש', 'ת'];

    public function calendar(): CalendarInterface
    {
        return new JewishCalendar();
    }

    protected function formatJewishYear(AbstractCalendarDate $date): string
    {
        // Hebrew locale traditionally omits the thousands-marker for Jewish years.
        return (new JewishCalendar())->numberToHebrewNumerals($date->year(), false);
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Hebrew genitive: "של ה" (shel ha-) = "of the"
        $he = static fn (string $s): array => [$s, '%s של ה' . $s];

        // When nominative and genitive stems differ
        $he2 = static fn (string $nom, string $gen): array => [$nom, '%s של ה' . $gen];

        return [
            // Adopted
            Relationship::fixed(...$he('אם מאמצת'))->adoptive()->mother(),
            Relationship::fixed(...$he('אב מאמץ'))->adoptive()->father(),
            Relationship::fixed(...$he('הורה מאמץ'))->adoptive()->parent(),
            Relationship::fixed(...$he('בת מאומצת'))->adopted()->daughter(),
            Relationship::fixed(...$he('בן מאומץ'))->adopted()->son(),
            Relationship::fixed(...$he('ילד/ה מאומץ/צת'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$he('אם אומנת'))->fostering()->mother(),
            Relationship::fixed(...$he('אב אומן'))->fostering()->father(),
            Relationship::fixed(...$he('הורה אומן'))->fostering()->parent(),
            Relationship::fixed(...$he('בת אומנה'))->fostered()->daughter(),
            Relationship::fixed(...$he('בן אומנה'))->fostered()->son(),
            Relationship::fixed(...$he('ילד/ה אומנה'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$he('אם'))->mother(),
            Relationship::fixed(...$he('אב'))->father(),
            Relationship::fixed(...$he('הורה'))->parent(),
            // Children
            Relationship::fixed(...$he('בת'))->daughter(),
            Relationship::fixed(...$he('בן'))->son(),
            Relationship::fixed(...$he('ילד/ה'))->child(),
            // Siblings
            Relationship::fixed(...$he('אחות תאומה'))->twin()->sister(),
            Relationship::fixed(...$he('אח תאום'))->twin()->brother(),
            Relationship::fixed(...$he('תאום/ה'))->twin()->sibling(),
            Relationship::fixed(...$he('אחות גדולה'))->older()->sister(),
            Relationship::fixed(...$he('אח גדול'))->older()->brother(),
            Relationship::fixed(...$he('אחות קטנה'))->younger()->sister(),
            Relationship::fixed(...$he('אח קטן'))->younger()->brother(),
            Relationship::fixed(...$he2('אחות', 'אחות'))->sister(),
            Relationship::fixed(...$he2('אח', 'אח'))->brother(),
            Relationship::fixed(...$he('אח/ות'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$he('אחות חורגת מהאב'))->father()->daughter(),
            Relationship::fixed(...$he('אח חורג מהאב'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$he('אחות חורגת מהאם'))->mother()->daughter(),
            Relationship::fixed(...$he('אח חורג מהאם'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$he('אחות חורגת'))->parent()->daughter(),
            Relationship::fixed(...$he('אח חורג'))->parent()->son(),
            Relationship::fixed(...$he('אח/ות חורג/ת'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$he('אם חורגת'))->parent()->wife(),
            Relationship::fixed(...$he('אב חורג'))->parent()->husband(),
            Relationship::fixed(...$he('בת חורגת'))->married()->spouse()->daughter(),
            Relationship::fixed(...$he('בן חורג'))->married()->spouse()->son(),
            Relationship::fixed(...$he('ילד/ה חורג/ת'))->married()->spouse()->child(),
            Relationship::fixed(...$he('בת חורגת'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$he('בן חורג'))->parent()->spouse()->son(),
            Relationship::fixed(...$he('ילד/ה חורג/ת'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$he('גרושה'))->divorced()->partner()->female(),
            Relationship::fixed(...$he('גרוש'))->divorced()->partner()->male(),
            Relationship::fixed(...$he('גרוש/ה'))->divorced()->partner(),
            Relationship::fixed(...$he('ארוסה'))->engaged()->partner()->female(),
            Relationship::fixed(...$he('ארוס'))->engaged()->partner()->male(),
            Relationship::fixed(...$he2('אישה', 'אישה'))->wife(),
            Relationship::fixed(...$he2('בעל', 'בעל'))->husband(),
            Relationship::fixed(...$he('בן/בת זוג'))->spouse(),
            Relationship::fixed(...$he('בן/בת זוג'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$he2('חמות', 'חמות'))->married()->spouse()->mother(),
            Relationship::fixed(...$he2('חם', 'חם'))->married()->spouse()->father(),
            Relationship::fixed(...$he('הורה של בן/בת הזוג'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$he2('כלה', 'כלה'))->child()->wife(),
            Relationship::fixed(...$he2('חתן', 'חתן'))->child()->husband(),
            Relationship::fixed(...$he('כלה/חתן'))->child()->married()->spouse(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$he('גיסה'))->spouse()->sister(),
            Relationship::fixed(...$he('גיס'))->spouse()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$he('גיסה'))->sibling()->wife(),
            Relationship::fixed(...$he('גיס'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$he2('סבתא', 'סבתא'))->parent()->mother(),
            Relationship::fixed(...$he2('סבא', 'סבא'))->parent()->father(),
            Relationship::fixed(...$he('סב/תא'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$he2('נכדה', 'נכדה'))->child()->daughter(),
            Relationship::fixed(...$he2('נכד', 'נכד'))->child()->son(),
            Relationship::fixed(...$he('נכד/ה'))->child()->child(),
            // Aunts/uncles
            Relationship::fixed(...$he2('דודה', 'דודה'))->parent()->sister(),
            Relationship::fixed(...$he2('דוד', 'דוד'))->parent()->brother(),
            // Nieces/nephews
            Relationship::fixed(...$he2('אחיינית', 'אחיינית'))->sibling()->daughter(),
            Relationship::fixed(...$he2('אחיין', 'אחיין'))->sibling()->son(),
            Relationship::fixed(...$he('אחיין/ית'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$he('בת דוד/ה'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$he('בן דוד/ה'))->parent()->sibling()->son(),
            Relationship::fixed(...$he('בן/בת דוד/ה'))->parent()->sibling()->child(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $he('דודה רבה' . ($n > 2 ? ' ×' . ($n - 1) : '')))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $he('דוד רבא' . ($n > 2 ? ' ×' . ($n - 1) : '')))->ancestor()->brother(),
            // Dynamic: grand-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $he('אחיינית גדולה' . ($n > 2 ? ' ×' . ($n - 1) : '')))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $he('אחיין גדול' . ($n > 2 ? ' ×' . ($n - 1) : '')))->sibling()->descendant()->male(),
            // Dynamic: ancestors
            Relationship::dynamic(static fn (int $n) => $he2('סבתא רבה' . ($n > 3 ? ' ×' . ($n - 2) : ''), 'סבתא רבה' . ($n > 3 ? ' ×' . ($n - 2) : '')))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $he2('סבא רבא' . ($n > 3 ? ' ×' . ($n - 2) : ''), 'סבא רבא' . ($n > 3 ? ' ×' . ($n - 2) : '')))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $he('סב/תא רב/ה' . ($n > 3 ? ' ×' . ($n - 2) : '')))->ancestor(),
            // Dynamic: descendants
            Relationship::dynamic(static fn (int $n) => $he2('נינה' . ($n > 3 ? ' ×' . ($n - 2) : ''), 'נינה' . ($n > 3 ? ' ×' . ($n - 2) : '')))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $he2('נין' . ($n > 3 ? ' ×' . ($n - 2) : ''), 'נין' . ($n > 3 ? ' ×' . ($n - 2) : '')))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $he('נין/ה' . ($n > 3 ? ' ×' . ($n - 2) : '')))->descendant(),
        ];
    }
}
