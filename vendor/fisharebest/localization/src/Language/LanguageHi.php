<?php namespace Fisharebest\Localization;

/**
 * Class LanguageHi - Representation of the Hindi language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHi extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'hi';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptDeva;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIn;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule2;
	}
}
