[![Build Status](https://travis-ci.org/fisharebest/localization.svg?branch=master)](https://travis-ci.org/fisharebest/localization)
[![Coverage Status](https://img.shields.io/coveralls/fisharebest/localization.svg)](https://coveralls.io/r/fisharebest/localization?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a252b4b3-62c1-40bd-be44-43a7dc6e4a9b/mini.png)](https://insight.sensiolabs.com/projects/a252b4b3-62c1-40bd-be44-43a7dc6e4a9b)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fisharebest/localization/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fisharebest/localization/?branch=master)
[![Code Climate](https://codeclimate.com/github/fisharebest/localization/badges/gpa.svg)](https://codeclimate.com/github/fisharebest/localization)

Localization standards and data
===============================

This package combines localization data from many standards, including
the [Unicode CLDR](http://cldr.unicode.org),
[RFC5646 / IANA subtag registry](https://tools.ietf.org/html/rfc5646),
[ISO-3166](https://en.wikipedia.org/wiki/ISO_3166),
[ISO-639](https://en.wikipedia.org/wiki/ISO_639),
[ISO-15924](http://unicode.org/iso15924/),
etc., to help you produce applications that behave nicely for visitors from
around the world.

Locales, languages, scripts and territories
===========================================

A locale consists of three things: a language, a script and a territory.
Scripts and territories are often implicit from the language.

Normally you would just need to work with locales, and can ignore the
other entities.

``` php
$locale = new LocaleJa;   // Create a locale for Japanese.
$locale->code();          // "ja_JP" (territories are always included in locale codes)
$locale->languageTag();   // "ja" (redundant territories are omitted in tags)
$locale->endonym();       // "日本語" (Japanese name for Japanese)

// Languages - extract from the locale, or create with "new LanguageXx"
$locale->language();            // LanguageJa
$locale->language()->code();    // "ja" (ISO-639 code)

// Scripts - extract from the locale, or create with "new ScriptXxxx"
$locale->script();              // ScriptJpan
$locale->script()->code();      // "Jpan" (ISO-15924 code)
$locale->script()->direction(); // "ltr" (left to right)

// Territories - extract from the locale, or create with "new TerritoryXx"
$locale->territory();           // TerritoryJp
$locale->territory()->code();   // "JP" (ISO-3166 code)

// A few locales can also specify variants.
$locale = new LocaleCaValencia; // The Valencian dialect of Catalan
$locale->variant();             // VariantValencia
$locale->variant()->code();     // "valencia"
```

Localization
============

Create a locale and use it to localize data in your application.

``` php
// Two ways to create locales
$locale = new LocaleEnGb;
$locale = Locale::create('en-GB');

// Markup for HTML elements containing this locale
$locale->htmlAttributes();      // lang="ar" dir="rtl"

// Is text written left-to-right or right-to-left
$locale->direction();           // "ltr" or "rtl"

// Days of the week.
$locale->firstDay();            // 0=Sunday, 1=Monday, etc.
$locale->weekendStart();        // 0=Sunday, 1=Monday, etc.
$locale->weekendEnd();          // 0=Sunday, 1=Monday, etc.

// Measurement systems and paper sizes.
$locale->measurementSystem();   // "metric" or "US"
$locale->paperSize();           // "A4" or "US-Letter"

// Formatting numbers
$locale = new LocaleGr;         // Gujarati
$locale->digits('2014');        // "૨૦૧૪"
$locale = new LocaleItCh;       // Swiss Italian
$locale->number('12345678.9');  // "12'345'678.9"
$locale->percent(0.1234, 1);    // "12.3%"

// To sort data properly in MySQL, you need to specify a collation sequence.
// See http://dev.mysql.com/doc/refman/5.7/en/charset-unicode-sets.html
$locale->collation();           // "unicode_ci", "swedish_ci", etc.
```

Updates welcome
===============

Please provide references to sources, such as:

* [CLDR](http://localization.unicode.org)
* [Ethnologue](https://www.ethnologue.com)
* [ScriptSource](https://www.scriptsource.org)
