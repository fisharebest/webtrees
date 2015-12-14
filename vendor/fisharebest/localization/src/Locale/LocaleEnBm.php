<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBm;

/**
 * Class LocaleEnBm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnBm extends LocaleEn {
	public function territory() {
		return new TerritoryBm;
	}
}
