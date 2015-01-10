CHANGE LOG
==========

## 1.3.0 (2014-10-31)
 - Only emulate bugs in the shim functions, not when using the calendar classes.

## 1.2.0 (2014-09-27)
 - Improve coverage of unit tests.
 - Remove dependency on mb_string - generate Hebrew text directly in ISO-8859-8.
 - Code style tips from scrutinizer-ci.com.

## 1.1.2 (2014-09-16)
 - Fix #1; add support for the third parameter in jdtojewish().

## 1.1.1 (2014-09-14)
 - Cannot inherit abstract classes in PHP 5.3.0 - 5.3.8.

## 1.1.0 (2014-09-13)
 - Add support for Arabic (Hijri) and Persian (Jalali) calendars.
 - Convert logic to lookup tables for better performance.

## 1.0.3 (2014-09-12)
 - Simplify the way shims are created.

## 1.0.2 (2014-09-11)
 - Wrong encoding of Hebrew dates.
 - Emulate PHP bug 54254 in Hebrew dates.

## 1.0.1 (2014-09-11)
 - Improve coverage of unit tests.
 - Convert logic to lookup tables for better performance.

## 1.0.0 (2014-09-10)
 - Initial release, with support for all the functions and constants from `ext/calendar`.
