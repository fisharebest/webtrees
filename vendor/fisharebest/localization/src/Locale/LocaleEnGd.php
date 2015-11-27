<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGd;

/**
 * Class LocaleEnGd
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnGd extends LocaleEn {
	public function territory() {
		return new TerritoryGd;
	}
}
