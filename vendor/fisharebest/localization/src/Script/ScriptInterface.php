<?php namespace Fisharebest\Localization\Script;

/**
 * Interface ScriptInterface - Representation of a writing system.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
interface ScriptInterface {
	/**
	 * The ISO15924 code for this script.
	 *
	 * @return string
	 */
	public function code();

	/**
	 * Is the script usually written left-to-right “ltr” or right-to-left “rtl”.
	 *
	 * @return string “ltr” or “rtl”
	 */
	public function direction();

	/**
	 * The ISO15924 number for this script.
	 *
	 * @return string
	 */
	public function number();

	/**
	 * What are the default digits used by this script?
	 *
	 * This is an array of translations from Hindu-Arabic (0123456789)
	 * symbols to other symbols.  For English, etc., this array is empty.
	 *
	 * Some locales (e.g. Persian) use their own digits, rather than
	 * the default digits of their script.
	 *
	 * @return string[]
	 */
	public function numerals();

	/**
	 * The Unicode name (aka “property value alias”) for this script, or
	 * null if one does not exist.
	 *
	 * @return string|null
	 */
	public function unicodeName();
}
