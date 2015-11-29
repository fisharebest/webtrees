<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBj;

/**
 * Class LocaleYoBj
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleYoBj extends LocaleYo {
	public function territory() {
		return new TerritoryBj;
	}
}
