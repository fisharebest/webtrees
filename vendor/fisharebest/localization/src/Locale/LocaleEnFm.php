<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryFm;

/**
 * Class LocaleEnFm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnFm extends LocaleEn {
	public function territory() {
		return new TerritoryFm;
	}
}
