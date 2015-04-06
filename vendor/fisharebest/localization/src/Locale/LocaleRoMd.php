<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMd;

/**
 * Class LocaleRoMd - Moldavian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRoMd extends LocaleRo {
	/** {@inheritdoc} */
	public function endonym() {
		return 'moldovenească';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'MOLDOVENEASCA';
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMd;
	}
}
