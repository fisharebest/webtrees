<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryZm;

/**
 * Class LocaleEnZm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnZm extends LocaleEn {
	public function territory() {
		return new TerritoryZm;
	}
}
