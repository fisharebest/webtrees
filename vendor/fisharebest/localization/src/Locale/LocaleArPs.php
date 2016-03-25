<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPs;

/**
 * Class LocaleArPs
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArPs extends LocaleAr {
	public function territory() {
		return new TerritoryPs;
	}
}
