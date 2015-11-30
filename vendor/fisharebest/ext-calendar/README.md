[![Build Status](https://travis-ci.org/fisharebest/ext-calendar.svg?branch=master)](https://travis-ci.org/fisharebest/ext-calendar)
[![Coverage Status](https://coveralls.io/repos/fisharebest/ext-calendar/badge.png)](https://coveralls.io/r/fisharebest/ext-calendar)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/952d6e11-6941-447b-9757-fc8dbc3d2a1f/mini.png)](https://insight.sensiolabs.com/projects/952d6e11-6941-447b-9757-fc8dbc3d2a1f)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fisharebest/ext-calendar/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fisharebest/ext-calendar/?branch=master)
[![Code Climate](https://codeclimate.com/github/fisharebest/ext-calendar/badges/gpa.svg)](https://codeclimate.com/github/fisharebest/ext-calendar)

PHP calendar functions
======================

This package provides an implementation of the
[Arabic (Hijri)](https://en.wikipedia.org/wiki/Islamic_calendar),
[French Republican](https://en.wikipedia.org/wiki/French_Republican_Calendar),
[Gregorian](https://en.wikipedia.org/wiki/Gregorian_calendar),
[Julian](https://en.wikipedia.org/wiki/Julian_calendar),
[Jewish](https://en.wikipedia.org/wiki/Hebrew_calendar) and
[Persian (Jalali)](https://en.wikipedia.org/wiki/Iranian_calendars) calendars, plus
a replacement for PHP‘s [ext/calendar](https://php.net/calendar) extension.
It allows you to use the following PHP functions on servers that do not have the
ext/calendar extension installed (such as HHVM).

* [cal_days_in_month()](https://php.net/cal_days_in_month)
* [cal_from_jd()](https://php.net/cal_from_jd)
* [cal_info()](https://php.net/cal_info)
* [cal_to_jd()](https://php.net/cal_to_jd)
* [easter_date()](https://php.net/easter_date)
* [easter_days()](https://php.net/easter_days)
* [FrenchToJD()](https://php.net/FrenchToJD)
* [GregorianToJD()](https://php.net/GregorianToJD)
* [JDDayOfWeek()](https://php.net/JDDayOfWeek)
* [JDMonthName()](https://php.net/JDMonthName)
* [JDToFrench()](https://php.net/JDToFrench)
* [JDToGregorian()](https://php.net/JDToGregorian)
* [jdtojewish()](https://php.net/jdtojewish)
* [JDToJulian()](https://php.net/JDToJulian)
* [jdtounix()](https://php.net/jdtounix)
* [JewishToJD()](https://php.net/JewishToJD)
* [JulianToJD()](https://php.net/JulianToJD)
* [unixtojd()](https://php.net/unixtojd)

How to use it
=============

Add the package as a dependency in your `composer.json` file:

``` javascript
require {
    "fisharebest/ext-calendar": "2.*"
}
```

Now you can use the PHP functions, whether `ext/calendar` is installed or not.
Since version 2.2, it is no longer necessary to initialise these using `Shim::create()`.

``` php
require 'vendor/autoload.php';
print_r(cal_info(CAL_GREGORIAN)); // Works in HHVM, or if ext-calendar is not installed
```

Alternatively, just use the calendar classes directly.

``` php
use Fisharebest\ExtCalendar;

// Create a calendar
$calendar = new ArabicCalendar;
$calendar = new FrenchCalendar;
$calendar = new GregorianCalendar;
$calendar = new JewishCalendar;
$calendar = new JulianCalendar;
$calendar = new PersianCalendar;

// Date conversions
$julian_day = $calendar->ymdToJd($year, $month, $day);
list($year, $month, $day) = $calendar->jdToYmd($julian_day);

// Information about days, weeks and months
$is_leap_year   = $calendar->isLeapYear($year);
$days_in_month  = $calendar->daysInMonth($year, $month);
$months_in_year = $calendar->monthsInYear();  // Includes leap-months
$days_in_week   = $calendar->daysInWeek();    // Not all calendars have 7!

// Which dates are valid for this calendar?
$jd = $calendar->jdStart();
$jd = $calendar->jdEnd();

// Miscellaneous utilities
$jewish = new JewishCalendar;
$jewish->numberToHebrewNumerals(5781, false); // "תשפ״א"
$jewish->numberToHebrewNumerals(5781, true);  // "ה׳תשפ״א"
```

Known restrictions and limitations
==================================

When faced with invalid inputs, the shim functions trigger `E_USER_WARNING` instead of `E_WARNING`.  The text of the error messages is the same.

The functions `easterdate()` and `jdtounixtime()` use PHP‘s timezone, instead of the operating system‘s timezone.  These may be different.

Compatibility with different versions of PHP
============================================

The following PHP bugs are emulated, according to the version of PHP being used.
Thus the package always provides the same behaviour as the native `ext/calendar` extension.

* [#54254](https://bugs.php.net/bug.php?id=54254) Jewish month "Adar" - fixed in PHP 5.5.

* [#67960](https://bugs.php.net/bug.php?id=67960) Constants `CAL_DOW_SHORT` and `CAL_DOW_LONG` - found and fixed by this project - fixed in PHP 5.5.21 and 5.6.5.

* [#67976](https://bugs.php.net/bug.php?id=67976) Wrong value in `cal_days_in_month()` for French calendar - found by this project.

Development and contributions
=============================

Due to the known restrictions above, you may need to run unit tests using `TZ=UTC phpunit`.

Pull requests are welcome.  Please ensure you include unit-tests where
applicable, and follow the existing coding conventions.  These are to follow
[PSR](http://www.php-fig.org/) standards, except for:

* tabs are used for indentation
* opening braces always go on the end of the previous line

History
=======

These functions were originally written for the [webtrees](http://www.webtrees.net)
project.  As part of a refactoring process, they were extracted to a standalone
library, given version numbers, unit tests, etc.

Future plans
============

* Support alternate leap-year schemes for the French calendar (true equinox, Romme, 128-year cycle) as well as the 4-year cycle.
* Support other calendars, such as Ethiopian, Hindu, Chinese, etc.
