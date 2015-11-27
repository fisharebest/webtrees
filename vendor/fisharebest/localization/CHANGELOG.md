CHANGE LOG
==========

## 1.7.0 (2015-11-27)
 - Updated version of CLDR

## 1.6.0 (2015-09-14)
 - Updated versions of ISO-15924, iana-subtag-registry and CLDR

## 1.5.1 (2015-09-14)
 - Norwegian collation rules
 - Add PHP 7.0 and HHVM to Travis tests

## 1.5.0 (2015-06-29)
 - Updated versions of ISO-15924, iana-subtag-registry and CLDR

## 1.4.1 (2015-04-06)
 - PHP<=5.3.8 cannot extend abstract classes with abstract functions

## 1.4.0 (2015-03-27)
 - Fix PSR4 autoloading
 - Add PHP interfaces for scripts, languages, locales, etc
 - Add several missing plural rules
 - Add Locale::httpAcceptLanguage() to negotiate languages

## 1.3.2 (2015-03-23)
 - Gettext detection fails on 32 bit builds

## 1.3.1 (2015-03-21)
 - Add plural rules for Yiddish (yi) and Divehi (dv)

## 1.3.0 (2015-03-20)
 - Add Translator/Translation classes

## 1.2.0 (2015-03-19)
 - Add plural rules for working with translations
 - Add missing tests for formatting percentages

## 1.1.2 (2015-03-15)
 - Do not suppress the script in the language tag for zh-Hans

## 1.1.1 (2015-03-13)
 - Fix endonym (sr-Latn)

## 1.1.0 (2015-03-12)
 - Add Divehi (dv)
 - Add Maori (mi)
 - Add Occitan (oc)
 - Add Tatar (tt)

## 1.0.3 (2015-03-11)
 - Tests timed out on Travis-CS
 - Add failure information to exception

## 1.0.2 (2015-03-11)
 - Add Locale::create() to create Locale objects from language tags and locale codes.

## 1.0.1 (2015-02-27)
 - Add Locale::number() to format numbers.
 - Add Locale::percent() to format percentages.
 - Add Locale::htmlAttributes() for HTML elements.
 - Some locales, such as shi-Tfng, are written left-to-right, even though tifinagh is a right to left script.
 - Improve some sortable endonyms.

## 1.0.0 (2015-02-24)
 - Initial release, with support for locales, languages, territories, scripts and variants.
