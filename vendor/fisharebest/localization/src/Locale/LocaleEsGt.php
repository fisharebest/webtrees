<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGt;

/**
 * Class LocaleEsGt
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsGt extends LocaleEs {
	public function territory() {
		return new TerritoryGt;
	}
}
