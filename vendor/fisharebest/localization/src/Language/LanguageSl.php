<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSl - Representation of the Slovenian language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSl extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'sl';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritorySi;
	}
}
