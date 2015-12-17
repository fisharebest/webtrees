<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPy;

/**
 * Class LocaleEsPy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsPy extends LocaleEs {
	public function territory() {
		return new TerritoryPy;
	}
}
