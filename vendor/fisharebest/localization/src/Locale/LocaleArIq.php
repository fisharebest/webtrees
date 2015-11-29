<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIq;

/**
 * Class LocaleArIq
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArIq extends LocaleAr {
	public function territory() {
		return new TerritoryIq;
	}
}
