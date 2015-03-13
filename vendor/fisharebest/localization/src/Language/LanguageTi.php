<?php namespace Fisharebest\Localization;

/**
 * Class LanguageTi - Representation of the Tigrinya language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTi extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ti';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptEthi;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryEt;
	}
}
