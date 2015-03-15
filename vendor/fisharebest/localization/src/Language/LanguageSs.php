<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSs - Representation of the Swati language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSs extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ss';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritorySz;
	}
}
