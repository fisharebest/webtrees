<?php namespace Fisharebest\Localization;

/**
 * Class LanguageAm - Representation of the Amharic language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageAm extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'am';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptEthi;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryEt;
	}

	/** {@inheritdoc} */
	public function pluralRule() {
		return new PluralRule2;
	}
}
