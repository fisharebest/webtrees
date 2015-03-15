<?php namespace Fisharebest\Localization;

/**
 * Class LanguageHy - Representation of the Armenian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageHy extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'hy';
	}

	/** {@inheritdoc} */
	public function defaultScript() {
		return new ScriptArmn;
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryAm;
	}
}
