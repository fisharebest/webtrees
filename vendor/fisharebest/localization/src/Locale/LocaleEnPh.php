<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPh;

/**
 * Class LocaleEnPh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnPh extends LocaleEn {
	public function territory() {
		return new TerritoryPh;
	}
}
