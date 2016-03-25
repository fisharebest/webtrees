<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryNa;

/**
 * Class LocaleAfNa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAfNa extends LocaleAf {
	public function territory() {
		return new TerritoryNa;
	}
}
