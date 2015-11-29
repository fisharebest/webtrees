<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIl;

/**
 * Class LocaleEnIl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnIl extends LocaleEn {
	public function territory() {
		return new TerritoryIl;
	}
}
