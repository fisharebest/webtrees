<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryNz;

/**
 * Class LocaleEnNz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnNz extends LocaleEn {
	public function territory() {
		return new TerritoryNz;
	}
}
