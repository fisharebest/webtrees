<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPg;

/**
 * Class LocaleEnPg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnPg extends LocaleEn {
	public function territory() {
		return new TerritoryPg;
	}
}
