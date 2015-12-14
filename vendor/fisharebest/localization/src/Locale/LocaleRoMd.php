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
	public function endonym() {
		return 'moldovenească';
	}

	public function endonymSortable() {
		return 'MOLDOVENEASCA';
	}

	public function territory() {
		return new TerritoryMd;
	}
}
