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

use Collator;
use Fisharebest\ExtCalendar\CalendarInterface;
use Fisharebest\ExtCalendar\GregorianCalendar;
use Fisharebest\ExtCalendar\JewishCalendar;
use Fisharebest\Webtrees\Contracts\LanguageInterface;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\AbstractCalendarDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Enums\CalendarEscape;
use Fisharebest\Webtrees\Enums\DateType;
use Fisharebest\Webtrees\Enums\PluralRule;
use Fisharebest\Webtrees\Enums\Script;
use Fisharebest\Webtrees\Enums\TextDirection;
use Fisharebest\Webtrees\Enums\Weekday;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Services\RomanNumeralsService;
use Normalizer;

use function abs;
use function mb_strtolower;
use function mb_strtoupper;
use function mb_substr;
use function normalizer_normalize;

abstract readonly class AbstractLanguage implements LanguageInterface
{
    protected const string DATE_ABOUT       = 'about %s';
    protected const string DATE_AFTER       = 'after %s';
    protected const string DATE_BEFORE      = 'before %s';
    protected const string DATE_BETWEEN_AND = 'between %s and %s';
    protected const string DATE_CALCULATED  = 'calculated %s';
    protected const string DATE_ESTIMATED   = 'estimated %s';
    protected const string DATE_EXACT       = '%s';
    protected const string DATE_FROM        = 'from %s';
    protected const string DATE_FROM_TO     = 'from %s to %s';
    protected const string DATE_INTERPRETED = 'interpreted %s';
    protected const string DATE_TO          = 'to %s';

    protected const string ERA_BCE = '%s' . UTF8::NO_BREAK_SPACE . 'ʙᴄᴇ';
    protected const string ERA_CE  = '%s' . UTF8::NO_BREAK_SPACE . 'ᴄᴇ';

    protected const string    ERA_HIJRI               = '%s' . UTF8::NO_BREAK_SPACE . 'ᴀʜ';
    protected const string    ERA_JALALI              = '%s' . UTF8::NO_BREAK_SPACE . 'sʜ';
    protected const string    ERA_JEWISH              = '%s' . UTF8::NO_BREAK_SPACE . 'ᴀᴍ';
    protected const string    ERA_JULIAN              = 'ᴀᴅ' . UTF8::NO_BREAK_SPACE . '%s';
    protected const string    ERA_ROMAN               = '%s' . UTF8::NO_BREAK_SPACE . 'ᴀᴜᴄ';
    protected const string    LIST_SEPARATOR          = ', ';
    protected const string    LIST_SEPARATOR_AND      = ', ';
    protected const string    LIST_SEPARATOR_OR       = ', ';
    protected const string    ENDONYM                 = 'und';
    protected const Weekday   FIRST_DAY               = Weekday::Monday;
    protected const string    LANGUAGE_TAG            = 'und';
    protected const string    LOCALE_CODE             = 'en_US@collation=phonebook';
    protected const int       DIGITS_FIRST_GROUP      = 3;
    protected const int       DIGITS_GROUP            = 3;
    protected const int       MINIMUM_GROUPING_DIGITS = 1;
    protected const array     DIGITS                  = [];
    protected const string    DIGITS_SEPARATOR        = ',';
    protected const string    NEGATIVE_SYMBOL         = '-';
    protected const string    DECIMAL_SYMBOL          = '.';
    protected const string    PERCENT_FORMAT          = '%s%%';
    protected const PaperSize PAPER_SIZE              = PaperSize::A4;
    protected const PluralRule PLURAL_RULE             = PluralRule::OneForm;
    protected const Script    SCRIPT                  = Script::Latn;

    /** @var array<int,string> */
    protected const array GREGORIAN_MONTHS_NOMINATIVE = [];

    /** @var array<int,string> */
    protected const array GREGORIAN_MONTHS_GENITIVE = [];

    /** @var array<int,string> */
    protected const array GREGORIAN_MONTHS_LOCATIVE = [];

    /** @var array<int,string> */
    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = [];

    /** @var array<int,string> */
    protected const array JEWISH_MONTHS_NOMINATIVE = [];

    /** @var array<int,string> */
    protected const array JEWISH_MONTHS_GENITIVE = [];

    /** @var array<int,string> */
    protected const array JEWISH_MONTHS_LOCATIVE = [];

    /** @var array<int,string> */
    protected const array JEWISH_MONTHS_INSTRUMENTAL = [];

    /** @var array<int,string> */
    protected const array FRENCH_MONTHS_NOMINATIVE = [];

    /** @var array<int,string> */
    protected const array FRENCH_MONTHS_GENITIVE = [];

    /** @var array<int,string> */
    protected const array FRENCH_MONTHS_LOCATIVE = [];

    /** @var array<int,string> */
    protected const array FRENCH_MONTHS_INSTRUMENTAL = [];

    /** @var array<int,string> */
    protected const array HIJRI_MONTHS_NOMINATIVE = [];

    /** @var array<int,string> */
    protected const array HIJRI_MONTHS_GENITIVE = [];

    /** @var array<int,string> */
    protected const array HIJRI_MONTHS_LOCATIVE = [];

    /** @var array<int,string> */
    protected const array HIJRI_MONTHS_INSTRUMENTAL = [];

    /** @var array<int,string> */
    protected const array JALALI_MONTHS_NOMINATIVE = [];

    /** @var array<int,string> */
    protected const array JALALI_MONTHS_GENITIVE = [];

    /** @var array<int,string> */
    protected const array JALALI_MONTHS_LOCATIVE = [];

    /** @var array<int,string> */
    protected const array JALALI_MONTHS_INSTRUMENTAL = [];

    /** @var array<int,string> */
    protected const array ALPHABET = [];

    /**
     * @return array<int,string>
     */
    public function alphabet(): array
    {
        return static::ALPHABET;
    }

    protected function assembleDate(string $day, string $month, string $year): string
    {
        $parts = [];

        if ($day !== '') {
            $parts[] = $day;
        }

        if ($month !== '') {
            $parts[] = $month;
        }

        if ($year !== '') {
            $parts[] = $year;
        }

        return implode(' ', $parts);
    }

    protected function assembleDateDdotMY(string $day, string $month, string $year): string
    {
        $parts = [];

        if ($day !== '') {
            $parts[] = $day . '.';
        }

        if ($month !== '') {
            $parts[] = $month;
        }

        if ($year !== '') {
            $parts[] = $year;
        }

        return implode(' ', $parts);
    }

    public function calendar(): CalendarInterface
    {
        return new GregorianCalendar();
    }

    public function collator(): Collator
    {
        $collator = new Collator(static::LOCALE_CODE);

        // Ignore upper/lower case differences.
        $collator->setStrength(Collator::SECONDARY);

        return $collator;
    }

    public function dateOrder(): string
    {
        return 'DMY';
    }

    public function digits(string|int $string): string
    {
        return strtr((string) $string, static::DIGITS);
    }

    public function number(float $number): string
    {
        if ($number < 0) {
            $number   = -$number;
            $negative = static::NEGATIVE_SYMBOL;
        } else {
            $negative = '';
        }

        $parts  = explode('.', (string) $number, 2);
        $digits = $parts[0];

        if (strlen($digits) >= static::DIGITS_FIRST_GROUP + static::MINIMUM_GROUPING_DIGITS) {
            $todo   = substr($digits, 0, -static::DIGITS_FIRST_GROUP);
            $digits = static::DIGITS_SEPARATOR . substr($digits, -static::DIGITS_FIRST_GROUP);

            while (strlen($todo) >= static::DIGITS_GROUP + static::MINIMUM_GROUPING_DIGITS) {
                $digits = static::DIGITS_SEPARATOR . substr($todo, -static::DIGITS_GROUP) . $digits;
                $todo   = substr($todo, 0, -static::DIGITS_GROUP);
            }

            $digits = $todo . $digits;
        }

        if (count($parts) > 1) {
            $decimals = static::DECIMAL_SYMBOL . $parts[1];
        } else {
            $decimals = '';
        }

        return $this->digits($negative . $digits . $decimals);
    }

    public function percentage(float $number): string
    {
        return sprintf(static::PERCENT_FORMAT, $this->number($number * 100.0));
    }

    public function endonym(): string
    {
        return static::ENDONYM;
    }

    public function firstDay(): Weekday
    {
        return static::FIRST_DAY;
    }

    public function formatDate(Date $date): string
    {
        $date1 = $date->minimumDate();
        $date2 = $date->maximumDate();

        return match ($date->type) {
            DateType::Exact       => sprintf(static::DATE_EXACT, $this->formatNominativeDate($date1)),
            DateType::About       => sprintf(static::DATE_ABOUT, $this->formatGenitiveDate($date1)),
            DateType::Calculated  => sprintf(static::DATE_CALCULATED, $this->formatNominativeDate($date1)),
            DateType::Estimated   => sprintf(static::DATE_ESTIMATED, $this->formatNominativeDate($date1)),
            DateType::Interpreted => sprintf(static::DATE_INTERPRETED, $this->formatNominativeDate($date1)),
            DateType::Before      => sprintf(static::DATE_BEFORE, $this->formatInstrumentalDate($date1)),
            DateType::After       => sprintf(static::DATE_AFTER, $this->formatLocativeDate($date1)),
            DateType::From        => sprintf(static::DATE_FROM, $this->formatGenitiveDate($date1)),
            DateType::To          => sprintf(static::DATE_TO, $this->formatGenitiveDate($date1)),
            DateType::Between     => sprintf(static::DATE_BETWEEN_AND, $this->formatInstrumentalDate($date1), $this->formatInstrumentalDate($date2)),
            DateType::FromTo      => sprintf(static::DATE_FROM_TO, $this->formatGenitiveDate($date1), $this->formatGenitiveDate($date2)),
        };
    }

    protected function formatDay(AbstractCalendarDate $date): string
    {
        if ($date->day() === 0) {
            return '';
        }

        // Hebrew uses Hebrew digits for Hebrew dates.  Normal digits otherwise.
        if ($date instanceof JewishDate && $this->script() === Script::Hebr) {
            return (new JewishCalendar())->numberToHebrewNumerals($date->day(), true);
        }

        return $this->digits($date->day());
    }

    protected function formatFrenchYear(AbstractCalendarDate $date): string
    {
        return 'An ' . (new RomanNumeralsService())->numberToRomanNumerals($date->year());
    }

    protected function formatGenitiveDate(AbstractCalendarDate $date): string
    {
        return $this->assembleDate(
            $this->formatDay($date),
            $this->formatMonthGenitive($date),
            $this->formatYear($date),
        );
    }

    protected function formatGregorianYear(AbstractCalendarDate $date): string
    {
        $year = $this->digits(abs($date->year()));

        if ($date->year() < 0) {
            return sprintf(static::ERA_BCE, $year);
        }

        return $year;
    }

    protected function formatHijriYear(AbstractCalendarDate $date): string
    {
        return $this->digits($date->year());
    }

    protected function formatInstrumentalDate(AbstractCalendarDate $date): string
    {
        if ($date->day() !== 0) {
            return $this->formatGenitiveDate($date);
        }

        return $this->assembleDate(
            $this->formatDay($date),
            $this->formatMonthInstrumental($date),
            $this->formatYear($date),
        );
    }

    protected function formatJalaliYear(AbstractCalendarDate $date): string
    {
        return $this->digits($date->year());
    }

    protected function formatJewishYear(AbstractCalendarDate $date): string
    {
        if ($this->script() === Script::Hebr) {
            return (new JewishCalendar())->numberToHebrewNumerals($date->year(), true);
        }

        return $this->digits($date->year());
    }

    protected function formatJulianYear(AbstractCalendarDate $date): string
    {
        if ($date->year() < 0) {
            return sprintf(static::ERA_BCE, $this->digits(abs($date->year())));
        } elseif ($date->new_old_style && $date->year() > 1) {
            $year = sprintf('%d/%02d', $date->year() - 1, $date->year() % 100);
        } else {
            $year = (string) $date->year();
        }

        return sprintf(static::ERA_CE, $this->digits($year));
    }

    public function formatList(array $items): string
    {
        return implode(static::LIST_SEPARATOR, $items);
    }

    public function formatListAnd(array $items): string
    {
        $last = array_pop($items);

        if ($last === null) {
            return '';
        }

        if ($items === []) {
            return $last;
        }

        return implode(static::LIST_SEPARATOR, $items) . static::LIST_SEPARATOR_AND . $last;
    }

    public function formatListOr(array $items): string
    {
        $last = array_pop($items);

        if ($last === null) {
            return '';
        }

        if ($items === []) {
            return $last;
        }

        return implode(static::LIST_SEPARATOR, $items) . static::LIST_SEPARATOR_OR . $last;
    }

    protected function formatLocativeDate(AbstractCalendarDate $date): string
    {
        if ($date->day() !== 0) {
            return $this->formatGenitiveDate($date);
        }

        return $this->assembleDate(
            $this->formatDay($date),
            $this->formatMonthLocative($date),
            $this->formatYear($date),
        );
    }

    protected function formatMonthGenitive(AbstractCalendarDate $date): string
    {
        $month = $this->monthIndex($date);

        return match ($date->calendarEscape()) {
            CalendarEscape::French    => static::FRENCH_MONTHS_GENITIVE[$month],
            CalendarEscape::Gregorian => static::GREGORIAN_MONTHS_GENITIVE[$month],
            CalendarEscape::Hijri     => static::HIJRI_MONTHS_GENITIVE[$month],
            CalendarEscape::Jalali    => static::JALALI_MONTHS_GENITIVE[$month],
            CalendarEscape::Jewish    => static::JEWISH_MONTHS_GENITIVE[$month],
            CalendarEscape::Julian    => static::GREGORIAN_MONTHS_GENITIVE[$month],
            CalendarEscape::Roman     => static::GREGORIAN_MONTHS_GENITIVE[$month],
        };
    }

    protected function formatMonthInstrumental(AbstractCalendarDate $date): string
    {
        $month = $this->monthIndex($date);

        return match ($date->calendarEscape()) {
            CalendarEscape::French    => static::FRENCH_MONTHS_INSTRUMENTAL[$month],
            CalendarEscape::Gregorian => static::GREGORIAN_MONTHS_INSTRUMENTAL[$month],
            CalendarEscape::Hijri     => static::HIJRI_MONTHS_INSTRUMENTAL[$month],
            CalendarEscape::Jalali    => static::JALALI_MONTHS_INSTRUMENTAL[$month],
            CalendarEscape::Jewish    => static::JEWISH_MONTHS_INSTRUMENTAL[$month],
            CalendarEscape::Julian    => static::GREGORIAN_MONTHS_INSTRUMENTAL[$month],
            CalendarEscape::Roman     => static::GREGORIAN_MONTHS_INSTRUMENTAL[$month],
        };
    }

    protected function formatMonthLocative(AbstractCalendarDate $date): string
    {
        $month = $this->monthIndex($date);

        return match ($date->calendarEscape()) {
            CalendarEscape::French    => static::FRENCH_MONTHS_LOCATIVE[$month],
            CalendarEscape::Gregorian => static::GREGORIAN_MONTHS_LOCATIVE[$month],
            CalendarEscape::Hijri     => static::HIJRI_MONTHS_LOCATIVE[$month],
            CalendarEscape::Jalali    => static::JALALI_MONTHS_LOCATIVE[$month],
            CalendarEscape::Jewish    => static::JEWISH_MONTHS_LOCATIVE[$month],
            CalendarEscape::Julian    => static::GREGORIAN_MONTHS_LOCATIVE[$month],
            CalendarEscape::Roman     => static::GREGORIAN_MONTHS_LOCATIVE[$month],
        };
    }

    protected function formatMonthNominative(AbstractCalendarDate $date): string
    {
        $month = $this->monthIndex($date);

        return match ($date->calendarEscape()) {
            CalendarEscape::French    => static::FRENCH_MONTHS_NOMINATIVE[$month],
            CalendarEscape::Gregorian => static::GREGORIAN_MONTHS_NOMINATIVE[$month],
            CalendarEscape::Hijri     => static::HIJRI_MONTHS_NOMINATIVE[$month],
            CalendarEscape::Jalali    => static::JALALI_MONTHS_NOMINATIVE[$month],
            CalendarEscape::Jewish    => static::JEWISH_MONTHS_NOMINATIVE[$month],
            CalendarEscape::Julian    => static::GREGORIAN_MONTHS_NOMINATIVE[$month],
            CalendarEscape::Roman     => static::GREGORIAN_MONTHS_NOMINATIVE[$month],
        };
    }

    protected function formatNominativeDate(AbstractCalendarDate $date): string
    {
        if ($date->day() !== 0) {
            return $this->formatGenitiveDate($date);
        }

        return $this->assembleDate(
            $this->formatDay($date),
            $this->formatMonthNominative($date),
            $this->formatYear($date),
        );
    }

    protected function formatRomanYear(AbstractCalendarDate $date): string
    {
        $year = $this->digits($date->year());

        return sprintf(static::ERA_ROMAN, $year);
    }

    protected function formatYear(AbstractCalendarDate $date): string
    {
        if ($date->year() === 0) {
            return '';
        }

        // Hebrew uses latin digits for numbers, and Hebrew digits for dates.
        if ($date instanceof JewishDate && $this->script() === Script::Hebr) {
            return $this->formatJewishYear($date);
        }

        return match ($date->calendarEscape()) {
            CalendarEscape::French    => $this->formatFrenchYear($date),
            CalendarEscape::Gregorian => $this->formatGregorianYear($date),
            CalendarEscape::Hijri     => $this->formatHijriYear($date),
            CalendarEscape::Jalali    => $this->formatJalaliYear($date),
            CalendarEscape::Jewish    => $this->formatJewishYear($date),
            CalendarEscape::Julian    => $this->formatJulianYear($date),
            CalendarEscape::Roman     => $this->formatRomanYear($date),
        };
    }

    public function initialLetter(string $string): string
    {
        return mb_substr($string, 0, 1);
    }

    public function languageTag(): string
    {
        return static::LANGUAGE_TAG;
    }

    /**
     * The Jewish month arrays have 15 entries (0 + 14 month names) to accommodate
     * both "Adar I", "Adar II" (leap years) and "Adar" (non-leap years).
     * We need to adjust the month index accordingly.
     */
    private function monthIndex(AbstractCalendarDate $date): int
    {
        $month = $date->month();

        if ($date instanceof JewishDate) {
            if ($month > 6 && !$date->isLeapYear() || $month > 7 && $date->isLeapYear()) {
                return $month + 1;
            }
        }

        return $month;
    }

    /**
     * Ignore diacritics on letters - unless the language considers them a different letter.
     */
    public function normalize(string $text): string
    {
        // Decompose any combined characters.
        $decomposed = normalizer_normalize($text, Normalizer::FORM_KD);

        if ($decomposed === false) {
            // Invalid UTF8?
            return $text;
        }

        // Keep any diacritic marks that are significant to this language.
        $text = strtr($decomposed, $this->normalizeExceptions());

        // Remove the others.
        return preg_replace('/\p{M}/u', '', $text);
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [];
    }

    public function paperSize(): PaperSize
    {
        return static::PAPER_SIZE;
    }

    public function pluralRule(): PluralRule
    {
        return static::PLURAL_RULE;
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [];
    }

    public function script(): Script
    {
        return static::SCRIPT;
    }

    public function strtolower(string $string): string
    {
        return mb_strtolower($string);
    }

    public function strtoupper(string $string): string
    {
        return mb_strtoupper($string);
    }

    public function textDirection(): TextDirection
    {
        return $this->script()->textDirection();
    }
}
