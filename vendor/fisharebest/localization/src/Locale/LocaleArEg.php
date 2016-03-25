<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryEg;

/**
 * Class LocaleArEg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArEg extends LocaleAr {
	public function territory() {
		return new TerritoryEg;
	}
}
