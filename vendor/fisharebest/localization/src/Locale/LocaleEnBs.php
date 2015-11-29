<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBs;

/**
 * Class LocaleEnBs
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnBs extends LocaleEn {
	public function territory() {
		return new TerritoryBs;
	}
}
