<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMy;

/**
 * Class LocaleEnMy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnMy extends LocaleEn {
	public function territory() {
		return new TerritoryMy;
	}
}
