<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryQa;

/**
 * Class LocaleArQa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArQa extends LocaleAr {
	public function territory() {
		return new TerritoryQa;
	}
}
