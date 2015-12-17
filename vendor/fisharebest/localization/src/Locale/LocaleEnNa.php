<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryNa;

/**
 * Class LocaleEnNa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnNa extends LocaleEn {
	public function territory() {
		return new TerritoryNa;
	}
}
