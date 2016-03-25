<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryVg;

/**
 * Class LocaleEnVg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnVg extends LocaleEn {
	public function territory() {
		return new TerritoryVg;
	}
}
