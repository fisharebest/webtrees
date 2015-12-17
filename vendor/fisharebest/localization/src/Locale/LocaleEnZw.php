<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryZw;

/**
 * Class LocaleEnZw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnZw extends LocaleEn {
	public function territory() {
		return new TerritoryZw;
	}
}
