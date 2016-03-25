<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMt;

/**
 * Class LocaleEnMt
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnMt extends LocaleEn {
	public function territory() {
		return new TerritoryMt;
	}
}
