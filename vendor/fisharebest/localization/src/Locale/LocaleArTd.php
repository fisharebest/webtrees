<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryTd;

/**
 * Class LocaleArTd
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArTd extends LocaleAr {
	public function territory() {
		return new TerritoryTd;
	}
}
