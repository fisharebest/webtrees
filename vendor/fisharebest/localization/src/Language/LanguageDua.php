<?php namespace Fisharebest\Localization;

/**
 * Class LanguageDua - Representation of the Duala language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageDua extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'dua';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryCm;
	}
}
