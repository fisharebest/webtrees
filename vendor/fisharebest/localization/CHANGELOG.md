CHANGE LOG
==========

## 1.17.0 (2022-10-24)
 - Fix: wrong locale code for scripts with no unicode name
 - Update to CLDR-41
 - Update to CLDR-39
 - Update to CLDR-38.1
 - Update to CLDR-38
 - Update to CLDR-37
 - Update to CLDR-36.1
 - Update to CLDR-36
 - Update to CLDR-35.1
 - Update to CLDR-35
 - Add PHP 8.2 to the test matrix
 - Update to 2022-08-08 version of iana-subtag-registry

## 1.16.1 (2022-08-10)
 - Fix: plural rules for Farsi

## 1.16.0 (2022-07-07)
 - Fix: plural rules for Turkish
 - Add PHP 8.0 and PHP 8.1 to the test matrix
 - Update to the latest version of ISO-15924
 - Update to the latest version of iana-subtag-registry

## 1.15.1 (2019-07-02)
 - Fix: escaped characters in PO files.

## 1.15.0 (2019-06-22)
 - Improve language negotiation for Chinese
 - Add support for PO files.

## 1.14.0 (2019-03-09)
 - Better negotiation for browsers that request zh-CN instead of zh-Hans
 - Update to CLDR-34 and latest versions of iana-subtag-registry
 - Add PHP7.4 to the test matrix.

## 1.13.0 (2019-02-05)
 - Add PHP7.3 to the test matrix.
 - Fix: Finnish should use Swedish collation algorithm.

## 1.12.0 (2018-09-25)
 - Add Sundanese (su)

## 1.11.0 (2018-09-05)
 - Update to CLDR-33.1 and latest versions of ISO-15924 and iana-subtag-registry
 - Adopt PSR-12 code style

## 1.10.3 (2018-01-15)
 - Use short type names in PHPdoc

## 1.10.2 (2017-10-30)
 - Fix wrong collation for Czech.

## 1.10.1 (2017-10-24)
 - Add PHP7.1 and PHP7.2 to the test matrix.
 - Fix invalid PHP translation file causes unhandled error.

## 1.10.0 (2016-06-02)
 - Add Anglo-Saxon (ang).

## 1.9.0 (2016-03-26)
 - Update to CLDR-29.

## 1.8.0 (2016-03-26)
 - Updated version of ISO15924 and iana-subtag-registry.

## 1.7.0 (2015-11-27)
 - Update to CLDR-28

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
