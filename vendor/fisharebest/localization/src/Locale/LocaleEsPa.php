<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPa;

/**
 * Class LocaleEsPa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsPa extends LocaleEs {
	public function territory() {
		return new TerritoryPa;
	}
}
