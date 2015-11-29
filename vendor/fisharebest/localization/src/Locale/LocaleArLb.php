<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryLb;

/**
 * Class LocaleArLb
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArLb extends LocaleAr {
	public function territory() {
		return new TerritoryLb;
	}
}
