<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAr;

/**
 * Class LocaleEsAr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsAr extends LocaleEs {
	public function territory() {
		return new TerritoryAr;
	}
}
