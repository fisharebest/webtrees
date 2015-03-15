<?php namespace Fisharebest\Localization;

/**
 * Class LanguageOm - Representation of the Oromo language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageOm extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'om';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryEt;
	}
}
