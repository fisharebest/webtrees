<?php namespace Fisharebest\Localization;

/**
 * Class LanguageTzm - Representation of the Central Atlas Tamazight language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class LanguageTzm extends Language {
	/** {@inheritdoc} */
	public function code() {
		return 'tzm';
	}

	/** {@inheritdoc} */
	public function defaultTerritory() {
		return new TerritoryMa;
	}
}
