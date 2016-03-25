<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBh;

/**
 * Class LocaleArBh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArBh extends LocaleAr {
	public function territory() {
		return new TerritoryBh;
	}
}
