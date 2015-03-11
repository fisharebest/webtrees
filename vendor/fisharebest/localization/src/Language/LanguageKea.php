<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKea - Representation of the Kabuverdianu language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKea extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'kea';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCv;
	}
}
