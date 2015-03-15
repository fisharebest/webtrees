<?php namespace Fisharebest\Localization;

/**
 * Class LanguageWae - Representation of the Walser language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageWae extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'wae';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCh;
	}
}
