<?php namespace Fisharebest\Localization;

/**
 * Class LanguageLn - Representation of the Lingala language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageLn extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ln';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCd;
	}
}
