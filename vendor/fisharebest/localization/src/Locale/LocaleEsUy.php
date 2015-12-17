<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryUy;

/**
 * Class LocaleEsUy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsUy extends LocaleEs {
	public function territory() {
		return new TerritoryUy;
	}
}
