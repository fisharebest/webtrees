<?php namespace Fisharebest\Localization;

/**
 * Class LanguageDz - Representation of the Dzongkha language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageDz extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'dz';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptTibt;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryBt;
	}
}
