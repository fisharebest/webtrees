<?php namespace Fisharebest\Localization;

/**
 * Class LanguageMua - Representation of the Mundang language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageMua extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'mua';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
