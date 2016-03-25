<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryLs;

/**
 * Class LocaleEnLs
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnLs extends LocaleEn {
	public function territory() {
		return new TerritoryLs;
	}
}
