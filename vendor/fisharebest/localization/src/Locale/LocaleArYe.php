<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryYe;

/**
 * Class LocaleArYe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArYe extends LocaleAr {
	public function territory() {
		return new TerritoryYe;
	}
}
