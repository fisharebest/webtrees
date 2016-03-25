<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBb;

/**
 * Class LocaleEnBb
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnBb extends LocaleEn {
	public function territory() {
		return new TerritoryBb;
	}
}
