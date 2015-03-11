<?php namespace Fisharebest\Localization;

/**
 * Class LanguageTa - Representation of the Tamil language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTa extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ta';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptTaml;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryIn;
	}
}
