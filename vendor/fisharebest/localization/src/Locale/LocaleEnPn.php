<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPn;

/**
 * Class LocaleEnPn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnPn extends LocaleEn {
	public function territory() {
		return new TerritoryPn;
	}
}
