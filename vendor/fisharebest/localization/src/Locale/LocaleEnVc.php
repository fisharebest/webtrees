<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryVc;

/**
 * Class LocaleEnVc
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnVc extends LocaleEn {
	public function territory() {
		return new TerritoryVc;
	}
}
