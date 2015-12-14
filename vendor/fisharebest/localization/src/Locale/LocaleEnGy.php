<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGy;

/**
 * Class LocaleEnGy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnGy extends LocaleEn {
	public function territory() {
		return new TerritoryGy;
	}
}
