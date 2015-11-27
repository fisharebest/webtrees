<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryRw;

/**
 * Class LocaleEnRw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnRw extends LocaleEn {
	public function territory() {
		return new TerritoryRw;
	}
}
