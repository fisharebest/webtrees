<?php namespace Fisharebest\Localization;

/**
 * Class LanguageKl - Representation of the Kalaallisut language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageKl extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'kl';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryGl;
	}
}
