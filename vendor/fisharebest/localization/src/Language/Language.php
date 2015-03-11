<?php namespace Fisharebest\Localization;

/**
 * Class Language - Representation of a language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
abstract class Language {
	/**
	 * The ISO639 code for this language.
	 *
	 * @return string
	 */
	abstract public function code();

	/**
	 * The default territory where this language is spoken, which would
	 * normally be omitted from an IETF language tag.
	 *
	 * For example, we would normally omit the JP subtag from ja-JP.
	 *
	 * @return Script
	 */
	public function defaultTerritory() {
		return new Territory001;
	}

	/**
	 * The default script used to write this language, which would
	 * normally be omitted from an IETF language tag.
	 *
	 * For example, we would normally omit the Latn subtag from en-Latn.
	 *
	 * @return Script
	 */
	public function defaultScript() {
		return new ScriptLatn;
	}
}
