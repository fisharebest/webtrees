<?php namespace Fisharebest\Localization;

/**
 * Class LanguageIi - Representation of the Sichuan Yi language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageIi extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ii';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCn;
	}
}
