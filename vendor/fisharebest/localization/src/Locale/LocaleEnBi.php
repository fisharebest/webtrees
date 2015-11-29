<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBi;

/**
 * Class LocaleEnBi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnBi extends LocaleEn {
	public function territory() {
		return new TerritoryBi;
	}
}
