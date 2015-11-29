<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAe;

/**
 * Class LocaleArAe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArAe extends LocaleAr {
	public function territory() {
		return new TerritoryAe;
	}
}
