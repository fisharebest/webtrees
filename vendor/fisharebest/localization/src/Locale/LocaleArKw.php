<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryKw;

/**
 * Class LocaleArKw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArKw extends LocaleAr {
	public function territory() {
		return new TerritoryKw;
	}
}
