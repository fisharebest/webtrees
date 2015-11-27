<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGg;

/**
 * Class LocaleEnGg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnGg extends LocaleEn {
	public function territory() {
		return new TerritoryGg;
	}
}
