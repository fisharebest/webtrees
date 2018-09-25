<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees;

use DomainException;
use Fisharebest\ExtCalendar\GregorianCalendar;
use Fisharebest\Webtrees\Date\CalendarDate;
use Fisharebest\Webtrees\Date\FrenchDate;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\HijriDate;
use Fisharebest\Webtrees\Date\JalaliDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Date\JulianDate;
use Fisharebest\Webtrees\Date\RomanDate;

/**
 * A representation of GEDCOM dates and date ranges.
 *
 * Since different calendars start their days at different times, (civil
 * midnight, solar midnight, sunset, sunrise, etc.), we convert on the basis of
 * midday.
 *
 * We assume that years start on the first day of the first month. Where
 * this is not the case (e.g. England prior to 1752), we need to use modified
 * years or the OS/NS notation "4 FEB 1750/51".
 */
class Date
{
    /** @var string Optional qualifier, such as BEF, FROM, ABT */
    public $qual1 = '';

    /** @var CalendarDate  The first (or only) date */
    private $date1;

    /** @var string  Optional qualifier, such as TO, AND */
    public $qual2 = '';

    /** @var CalendarDate|null Optional second date */
    private $date2 = null;

    /** @var string ptional text, as included with an INTerpreted date */
    private $text = '';

    /**
     * Create a date, from GEDCOM data.
     *
     * @param string $date A date in GEDCOM format
     */
    public function __construct(string $date)
    {
        // Extract any explanatory text
        if (preg_match('/^(.*) ?[(](.*)[)]/', $date, $match)) {
            $date       = $match[1];
            $this->text = $match[2];
        }
        if (preg_match('/^(FROM|BET) (.+) (AND|TO) (.+)/', $date, $match)) {
            $this->qual1 = $match[1];
            $this->date1 = $this->parseDate($match[2]);
            $this->qual2 = $match[3];
            $this->date2 = $this->parseDate($match[4]);
        } elseif (preg_match('/^(TO|FROM|BEF|AFT|CAL|EST|INT|ABT) (.+)/', $date, $match)) {
            $this->qual1 = $match[1];
            $this->date1 = $this->parseDate($match[2]);
        } else {
            $this->date1 = $this->parseDate($date);
        }
    }

    /**
     * When we copy a date object, we need to create copies of
     * its child objects.
     */
    public function __clone()
    {
        $this->date1 = clone $this->date1;
        if ($this->date2 !== null) {
            $this->date2 = clone $this->date2;
        }
    }

    /**
     * Convert a calendar date, such as "12 JUN 1943" into calendar date object.
     *
     * A GEDCOM date range may have two calendar dates.
     *
     * @param string $date
     *
     * @throws DomainException
     *
     * @return CalendarDate
     */
    private function parseDate($date): CalendarDate
    {
        // Valid calendar escape specified? - use it
        if (preg_match('/^(@#D(?:GREGORIAN|JULIAN|HEBREW|HIJRI|JALALI|FRENCH R|ROMAN)+@) ?(.*)/', $date, $match)) {
            $cal  = $match[1];
            $date = $match[2];
        } else {
            $cal = '';
        }
        // A date with a month: DM, M, MY or DMY
        if (preg_match('/^(\d?\d?) ?(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC|TSH|CSH|KSL|TVT|SHV|ADR|ADS|NSN|IYR|SVN|TMZ|AAV|ELL|VEND|BRUM|FRIM|NIVO|PLUV|VENT|GERM|FLOR|PRAI|MESS|THER|FRUC|COMP|MUHAR|SAFAR|RABI[AT]|JUMA[AT]|RAJAB|SHAAB|RAMAD|SHAWW|DHUAQ|DHUAH|FARVA|ORDIB|KHORD|TIR|MORDA|SHAHR|MEHR|ABAN|AZAR|DEY|BAHMA|ESFAN) ?((?:\d{1,4}(?: B\.C\.)?|\d\d\d\d\/\d\d)?)$/', $date, $match)) {
            $d = $match[1];
            $m = $match[2];
            $y = $match[3];
        } elseif (preg_match('/^(\d{1,4}(?: B\.C\.)?|\d\d\d\d\/\d\d)$/', $date, $match)) {
            // A date with just a year
            $d = '';
            $m = '';
            $y = $match[1];
        } else {
            // An invalid date - do the best we can.
            $d = '';
            $m = '';
            $y = '';
            // Look for a 3/4 digit year anywhere in the date
            if (preg_match('/\b(\d{3,4})\b/', $date, $match)) {
                $y = $match[1];
            }
            // Look for a month anywhere in the date
            if (preg_match('/(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC|TSH|CSH|KSL|TVT|SHV|ADR|ADS|NSN|IYR|SVN|TMZ|AAV|ELL|VEND|BRUM|FRIM|NIVO|PLUV|VENT|GERM|FLOR|PRAI|MESS|THER|FRUC|COMP|MUHAR|SAFAR|RABI[AT]|JUMA[AT]|RAJAB|SHAAB|RAMAD|SHAWW|DHUAQ|DHUAH|FARVA|ORDIB|KHORD|TIR|MORDA|SHAHR|MEHR|ABAN|AZAR|DEY|BAHMA|ESFAN)/', $date, $match)) {
                $m = $match[1];
                // Look for a day number anywhere in the date
                if (preg_match('/\b(\d\d?)\b/', $date, $match)) {
                    $d = $match[1];
                }
            }
        }

        // Unambiguous dates - override calendar escape
        if (preg_match('/^(TSH|CSH|KSL|TVT|SHV|ADR|ADS|NSN|IYR|SVN|TMZ|AAV|ELL)$/', $m)) {
            $cal = '@#DHEBREW@';
        } else {
            if (preg_match('/^(VEND|BRUM|FRIM|NIVO|PLUV|VENT|GERM|FLOR|PRAI|MESS|THER|FRUC|COMP)$/', $m)) {
                $cal = '@#DFRENCH R@';
            } else {
                if (preg_match('/^(MUHAR|SAFAR|RABI[AT]|JUMA[AT]|RAJAB|SHAAB|RAMAD|SHAWW|DHUAQ|DHUAH)$/', $m)) {
                    $cal = '@#DHIJRI@'; // This is a WT extension
                } else {
                    if (preg_match('/^(FARVA|ORDIB|KHORD|TIR|MORDA|SHAHR|MEHR|ABAN|AZAR|DEY|BAHMA|ESFAN)$/', $m)) {
                        $cal = '@#DJALALI@'; // This is a WT extension
                    } elseif (preg_match('/^\d{1,4}( B\.C\.)|\d\d\d\d\/\d\d$/', $y)) {
                        $cal = '@#DJULIAN@';
                    }
                }
            }
        }

        // Ambiguous dates - don't override calendar escape
        if ($cal == '') {
            if (preg_match('/^(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)$/', $m)) {
                $cal = '@#DGREGORIAN@';
            } else {
                if (preg_match('/^[345]\d\d\d$/', $y)) {
                    // Year 3000-5999
                    $cal = '@#DHEBREW@';
                } else {
                    $cal = '@#DGREGORIAN@';
                }
            }
        }
        // Now construct an object of the correct type
        switch ($cal) {
            case '@#DGREGORIAN@':
                return new GregorianDate([
                    $y,
                    $m,
                    $d,
                ]);
            case '@#DJULIAN@':
                return new JulianDate([
                    $y,
                    $m,
                    $d,
                ]);
            case '@#DHEBREW@':
                return new JewishDate([
                    $y,
                    $m,
                    $d,
                ]);
            case '@#DHIJRI@':
                return new HijriDate([
                    $y,
                    $m,
                    $d,
                ]);
            case '@#DFRENCH R@':
                return new FrenchDate([
                    $y,
                    $m,
                    $d,
                ]);
            case '@#DJALALI@':
                return new JalaliDate([
                    $y,
                    $m,
                    $d,
                ]);
            case '@#DROMAN@':
                return new RomanDate([
                    $y,
                    $m,
                    $d,
                ]);
            default:
                throw new DomainException('Invalid calendar');
        }
    }

    /**
     * A list of supported calendars and their names.
     *
     * @return string[]
     */
    public static function calendarNames(): array
    {
        return [
            /* I18N: The gregorian calendar */
            'gregorian' => I18N::translate('Gregorian'),
            /* I18N: The julian calendar */
            'julian'    => I18N::translate('Julian'),
            /* I18N: The French calendar */
            'french'    => I18N::translate('French'),
            /* I18N: The Hebrew/Jewish calendar */
            'jewish'    => I18N::translate('Jewish'),
            /* I18N: The Arabic/Hijri calendar */
            'hijri'     => I18N::translate('Hijri'),
            /* I18N: The Persian/Jalali calendar */
            'jalali'    => I18N::translate('Jalali'),
        ];
    }

    /**
     * Convert a date to the preferred format and calendar(s) display.
     *
     * @param bool|null   $url               Wrap the date in a link to calendar.php
     * @param string|null $date_format       Override the default date format
     * @param bool|null   $convert_calendars Convert the date into other calendars
     *
     * @return string
     */
    public function display($url = false, $date_format = null, $convert_calendars = true)
    {
        // @TODO, This is set in index.php - but it is not safe to rely on globals.
        // We need a new DateFormatterService class
        global $tree;

        $CALENDAR_FORMAT = $tree->getPreference('CALENDAR_FORMAT');

        if ($date_format === null) {
            $date_format = I18N::dateFormat();
        }

        if ($convert_calendars) {
            $calendar_format = explode('_and_', $CALENDAR_FORMAT);
        } else {
            $calendar_format = [];
        }

        // Two dates with text before, between and after
        $q1 = $this->qual1;
        $d1 = $this->date1->format($date_format, $this->qual1);
        $q2 = $this->qual2;
        if ($this->date2 === null) {
            $d2 = '';
        } else {
            $d2 = $this->date2->format($date_format, $this->qual2);
        }
        // Con vert to other calendars, if requested
        $conv1 = '';
        $conv2 = '';
        foreach ($calendar_format as $cal_fmt) {
            if ($cal_fmt != 'none') {
                $d1conv = $this->date1->convertToCalendar($cal_fmt);
                if ($d1conv->inValidRange()) {
                    $d1tmp = $d1conv->format($date_format, $this->qual1);
                } else {
                    $d1tmp = '';
                }
                if ($this->date2 === null) {
                    $d2conv = null;
                    $d2tmp  = '';
                } else {
                    $d2conv = $this->date2->convertToCalendar($cal_fmt);
                    if ($d2conv->inValidRange()) {
                        $d2tmp = $d2conv->format($date_format, $this->qual2);
                    } else {
                        $d2tmp = '';
                    }
                }
                // If the date is different from the unconverted date, add it to the date string.
                if ($d1 != $d1tmp && $d1tmp !== '') {
                    if ($url) {
                        if ($CALENDAR_FORMAT !== 'none') {
                            $conv1 .= ' <span dir="' . I18N::direction() . '">(<a href="' . $d1conv->calendarUrl($date_format) . '" rel="nofollow">' . $d1tmp . '</a>)</span>';
                        } else {
                            $conv1 .= ' <span dir="' . I18N::direction() . '"><br><a href="' . $d1conv->calendarUrl($date_format) . '" rel="nofollow">' . $d1tmp . '</a></span>';
                        }
                    } else {
                        $conv1 .= ' <span dir="' . I18N::direction() . '">(' . $d1tmp . ')</span>';
                    }
                }
                if ($this->date2 !== null && $d2 != $d2tmp && $d1tmp != '') {
                    if ($url) {
                        $conv2 .= ' <span dir="' . I18N::direction() . '">(<a href="' . $d2conv->calendarUrl($date_format) . '" rel="nofollow">' . $d2tmp . '</a>)</span>';
                    } else {
                        $conv2 .= ' <span dir="' . I18N::direction() . '">(' . $d2tmp . ')</span>';
                    }
                }
            }
        }

        // Add URLs, if requested
        if ($url) {
            $d1 = '<a href="' . $this->date1->calendarUrl($date_format) . '" rel="nofollow">' . $d1 . '</a>';
            if ($this->date2 !== null) {
                $d2 = '<a href="' . $this->date2->calendarUrl($date_format) . '" rel="nofollow">' . $d2 . '</a>';
            }
        }

        // Localise the date
        switch ($q1 . $q2) {
            case '':
                $tmp = $d1 . $conv1;
                break;
            case 'ABT':
                /* I18N: Gedcom ABT dates */
                $tmp = I18N::translate('about %s', $d1 . $conv1);
                break;
            case 'CAL':
                /* I18N: Gedcom CAL dates */
                $tmp = I18N::translate('calculated %s', $d1 . $conv1);
                break;
            case 'EST':
                /* I18N: Gedcom EST dates */
                $tmp = I18N::translate('estimated %s', $d1 . $conv1);
                break;
            case 'INT':
                /* I18N: Gedcom INT dates */
                $tmp = I18N::translate('interpreted %s (%s)', $d1 . $conv1, e($this->text));
                break;
            case 'BEF':
                /* I18N: Gedcom BEF dates */
                $tmp = I18N::translate('before %s', $d1 . $conv1);
                break;
            case 'AFT':
                /* I18N: Gedcom AFT dates */
                $tmp = I18N::translate('after %s', $d1 . $conv1);
                break;
            case 'FROM':
                /* I18N: Gedcom FROM dates */
                $tmp = I18N::translate('from %s', $d1 . $conv1);
                break;
            case 'TO':
                /* I18N: Gedcom TO dates */
                $tmp = I18N::translate('to %s', $d1 . $conv1);
                break;
            case 'BETAND':
                /* I18N: Gedcom BET-AND dates */
                $tmp = I18N::translate('between %s and %s', $d1 . $conv1, $d2 . $conv2);
                break;
            case 'FROMTO':
                /* I18N: Gedcom FROM-TO dates */
                $tmp = I18N::translate('from %s to %s', $d1 . $conv1, $d2 . $conv2);
                break;
            default:
                $tmp = I18N::translate('Invalid date');
                break;
        }

        if (strip_tags($tmp) === '') {
            return '';
        }

        return '<span class="date">' . $tmp . '</span>';
    }

    /**
     * Get the earliest calendar date from this GEDCOM date.
     *
     * In the date “FROM 1900 TO 1910”, this would be 1900.
     *
     * @return CalendarDate
     */
    public function minimumDate(): CalendarDate
    {
        return $this->date1;
    }

    /**
     * Get the latest calendar date from this GEDCOM date.
     *
     * In the date “FROM 1900 TO 1910”, this would be 1910.
     *
     * @return CalendarDate
     */
    public function maximumDate()
    {
        if ($this->date2 === null) {
            return $this->date1;
        }

        return $this->date2;
    }

    /**
     * Get the earliest Julian day number from this GEDCOM date.
     *
     * @return int
     */
    public function minimumJulianDay(): int
    {
        return $this->minimumDate()->minJD;
    }

    /**
     * Get the latest Julian day number from this GEDCOM date.
     *
     * @return int
     */
    public function maximumJulianDay(): int
    {
        return $this->maximumDate()->maxJD;
    }

    /**
     * Get the middle Julian day number from the GEDCOM date.
     *
     * For a month-only date, this would be somewhere around the 16th day.
     * For a year-only date, this would be somewhere around 1st July.
     *
     * @return int
     */
    public function julianDay(): int
    {
        return (int) (($this->minimumJulianDay() + $this->maximumJulianDay()) / 2);
    }

    /**
     * Offset this date by N years, and round to the whole year.
     *
     * This is typically used to create an estimated death date,
     * which is before a certain number of years after the birth date.
     *
     * @param int    $years     a number of years, positive or negative
     * @param string $qualifier typically “BEF” or “AFT”
     *
     * @return Date
     */
    public function addYears(int $years, string $qualifier = ''): Date
    {
        $tmp           = clone $this;
        $tmp->date1->y += $years;
        $tmp->date1->m = 0;
        $tmp->date1->d = 0;
        $tmp->date1->setJdFromYmd();
        $tmp->qual1 = $qualifier;
        $tmp->qual2 = '';
        $tmp->date2 = null;

        return $tmp;
    }

    /**
     * Calculate the the age of a person (n years), on a given date.
     *
     * @param Date $d1
     * @param Date $d2
     *
     * @return int
     */
    public static function getAgeYears(Date $d1, Date $d2): int
    {
        if ($d2->maximumJulianDay() >= $d1->minimumJulianDay() && $d2->minimumJulianDay() <= $d1->minimumJulianDay()) {
            // Overlapping dates
            $jd = $d1->minimumJulianDay();
        } else {
            // Non-overlapping dates
            $jd = $d2->minimumJulianDay();
        }

        if ($jd && $d1->minimumJulianDay() && $d1->minimumJulianDay() <= $jd) {
            return $d1->minimumDate()->getAge($jd);
        } else {
            return -1;
        }
    }

    /**
     * Calculate the the age of a person (n days), on a given date.
     *
     * @param Date $d1
     * @param Date $d2
     *
     * @return int
     */
    public static function getAgeDays(Date $d1, Date $d2): int
    {
        if ($d2->maximumJulianDay() >= $d1->minimumJulianDay() && $d2->minimumJulianDay() <= $d1->minimumJulianDay()) {
            // Overlapping dates
            $jd = $d1->minimumJulianDay();
        } else {
            // Non-overlapping dates
            $jd = $d2->minimumJulianDay();
        }

        // Days - integer only (for sorting, rather than for display)
        if ($jd && $d1->minimumJulianDay()) {
            return $jd - $d1->minimumJulianDay();
        } else {
            return -1;
        }
    }

    /**
     * Calculate the the age of a person, on a date.
     *
     * @param Date      $d1
     * @param Date|null $d2
     *
     * @return string
     */
    public static function getAge(Date $d1, Date $d2 = null): string
    {
        if ($d2 !== null) {
            if ($d2->maximumJulianDay() >= $d1->minimumJulianDay() && $d2->minimumJulianDay() <= $d1->minimumJulianDay()) {
                // Overlapping dates
                $jd = $d1->minimumJulianDay();
            } else {
                // Non-overlapping dates
                $jd = $d2->minimumJulianDay();
            }
        } else {
            // If second date not specified, use today’s date
            $jd = WT_CLIENT_JD;
        }

        // Just years, in local digits, with warning for negative/
        if ($jd && $d1->minimumJulianDay()) {
            if ($d1->minimumJulianDay() > $jd) {
                return '<i class="icon-warning"></i>';
            } else {
                $years = $d1->minimumDate()->getAge($jd);

                return I18N::number($years);
            }
        } else {
            return '';
        }
    }

    /**
     * Calculate the years/months/days between two events
     * Return a gedcom style age string: "1y 2m 3d" (for fact details)
     *
     * @param Date      $d1
     * @param Date|null $d2
     *
     * @return string
     */
    public static function getAgeGedcom(Date $d1, Date $d2 = null)
    {
        if ($d2 === null) {
            return $d1->date1->getAgeFull(WT_CLIENT_JD);
        }

        if (self::compare($d1, $d2) !== 0) {
            return $d1->date1->getAgeFull($d2->minimumJulianDay());
        }

        if ($d1->minimumJulianDay() == $d2->minimumJulianDay()) {
            return '0d';
        }

        return '';
    }

    /**
     * Compare two dates, so they can be sorted.
     *
     * return <0 if $a<$b
     * return >0 if $b>$a
     * return  0 if dates same/overlap
     * BEF/AFT sort as the day before/after
     *
     * @param Date $a
     * @param Date $b
     *
     * @return int
     */
    public static function compare(Date $a, Date $b)
    {
        // Get min/max JD for each date.
        switch ($a->qual1) {
            case 'BEF':
                $amin = $a->minimumJulianDay() - 1;
                $amax = $amin;
                break;
            case 'AFT':
                $amax = $a->maximumJulianDay() + 1;
                $amin = $amax;
                break;
            default:
                $amin = $a->minimumJulianDay();
                $amax = $a->maximumJulianDay();
                break;
        }
        switch ($b->qual1) {
            case 'BEF':
                $bmin = $b->minimumJulianDay() - 1;
                $bmax = $bmin;
                break;
            case 'AFT':
                $bmax = $b->maximumJulianDay() + 1;
                $bmin = $bmax;
                break;
            default:
                $bmin = $b->minimumJulianDay();
                $bmax = $b->maximumJulianDay();
                break;
        }
        if ($amax < $bmin) {
            return -1;
        }

        if ($amin > $bmax && $bmax > 0) {
            return 1;
        }

        if ($amin < $bmin && $amax <= $bmax) {
            return -1;
        }

        if ($amin > $bmin && $amax >= $bmax && $bmax > 0) {
            return 1;
        }

        return 0;
    }

    /**
     * Check whether a gedcom date contains usable calendar date(s).
     *
     * An incomplete date such as "12 AUG" would be invalid, as
     * we cannot sort it.
     *
     * @return bool
     */
    public function isOK(): bool
    {
        return $this->minimumJulianDay() && $this->maximumJulianDay();
    }

    /**
     * Calculate the gregorian year for a date. This should NOT be used internally
     * within WT - we should keep the code "calendar neutral" to allow support for
     * jewish/arabic users. This is only for interfacing with external entities,
     * such as the ancestry.com search interface or the dated fact icons.
     *
     * @return int
     */
    public function gregorianYear()
    {
        if ($this->isOK()) {
            $gregorian_calendar = new GregorianCalendar();
            list($year) = $gregorian_calendar->jdToYmd($this->julianDay());

            return $year;
        }

        return 0;
    }
}
