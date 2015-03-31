<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageInterface;
use Fisharebest\Localization\PluralRule\PluralRuleInterface;
use Fisharebest\Localization\Script\ScriptInterface;
use Fisharebest\Localization\Territory\TerritoryInterface;
use Fisharebest\Localization\Variant\VariantInterface;

/**
 * Interface LocaleInterface - Locale.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
interface LocaleInterface {
	/**
	 * Generate a linux locale code for this locale.  Examples include
	 * "fr", “en_GB”, “ca_ES@valencia” and “sr@latin”.
	 *
	 * @return string
	 */
	public function code();

	/**
	 * Which collation sequence should be used for this locale?
	 * “unicode_ci” would mean use “utf8_unicode_ci”, “utf8mb4_unicode_ci”, etc.
	 *
	 * @link http://dev.mysql.com/doc/refman/5.7/en/charset-unicode-sets.html
	 *
	 * @return string
	 */
	public function collation();

	/**
	 * Convert (Hindu-Arabic) digits into a localized form
	 *
	 * @param string $string  e.g. "123.45"
	 *
	 * @return string
	 */
	public function digits($string);

	/**
	 * Is text written left-to-right “ltr” or right-to-left “rtl”.
	 * Most scripts are only written in one direction, but there are a few that
	 * can be written in either direction.
	 *
	 * @return string “ltr” or “rtl”
	 */
	public function direction();

	/**
	 * The name of this locale, in its own language/script, and with the
	 * customary capitalization of the locale.
	 *
	 * @return string
	 */
	public function endonym();

	/**
	 * A sortable version of the locale name.  For example, “British English”
	 * might sort as “ENGLISH, BRITISH” to keep all the variants of English together.
	 *
	 * All-capitals makes sorting easier, as we can use a simple strcmp().
	 *
	 * @return string
	 */
	public function endonymSortable();

	/**
	 * Markup for an HTML element
	 *
	 * @return string e.g. lang="ar" dir="rtl"
	 */
	public function htmlAttributes();

	/**
	 * The language used by this locale.
	 *
	 * @return LanguageInterface
	 */
	public function language();

	/**
	 * The IETF language tag for the locale.  Examples include
	 * “fr, “en-GB”, “ca-ES-valencia” and “sr-Latn”.
	 *
	 * @return string
	 */
	public function languageTag();

	/**
	 * Convert (Hindu-Arabic) digits into a localized form
	 *
	 * @param string|float|integer $number The number to be localized
	 *
	 * @return string
	 */
	public function number($number);

	/**
	 * Convert (Hindu-Arabic) digits into a localized form
	 *
	 * @param string|float|integer $number The number to be localized
	 *
	 * @return string
	 */
	public function percent($number);

	/**
	 * Which plural rule is used in this locale
	 *
	 * @return PluralRuleInterface
	 */
	public function pluralRule();

	/**
	 * The script used by this locale.
	 *
	 * @return ScriptInterface
	 */
	public function script();

	/**
	 * The territory used by this locale.
	 *
	 * @return TerritoryInterface
	 */
	public function territory();

	/**
	 * The variant, if any of this locale.
	 *
	 * @return VariantInterface|null
	 */
	public function variant();
}
