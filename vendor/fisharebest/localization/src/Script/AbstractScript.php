<?php namespace Fisharebest\Localization\Script;

/**
 * Class AbstractScript - Representation of a writing system.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
abstract class AbstractScript {
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
	public function numerals() {
		return array();
	}

	/**
	 * Is the script written left-to-right “ltr” or right-to-left “rtl”.
	 *
	 * @return string “ltr” or “rtl”
	 */
	public function direction() {
		return substr_compare($this->number(), '1', 0, 1) ? 'ltr' : 'rtl';
	}

	/**
	 * The Unicode name (aka “property value alias”) for this script, or
	 * null if one does not exist.
	 *
	 * @return string|null
	 */
	public function unicodeName() {
		return null;
	}
}
