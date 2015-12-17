<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryTz;

/**
 * Class LocaleEnTz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnTz extends LocaleEn {
	public function territory() {
		return new TerritoryTz;
	}
}
