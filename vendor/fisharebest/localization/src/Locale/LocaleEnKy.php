<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryKy;

/**
 * Class LocaleEnKy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnKy extends LocaleEn {
	public function territory() {
		return new TerritoryKy;
	}
}
