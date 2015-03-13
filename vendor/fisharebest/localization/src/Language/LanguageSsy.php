<?php namespace Fisharebest\Localization;

/**
 * Class LanguageSsy - Representation of the Saho language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageSsy extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'ssy';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryEr;
	}
}
