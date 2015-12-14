<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIm;

/**
 * Class LocaleEnIm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnIm extends LocaleEn {
	public function territory() {
		return new TerritoryIm;
	}
}
