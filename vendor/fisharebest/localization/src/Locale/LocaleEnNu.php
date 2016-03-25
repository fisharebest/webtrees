<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryNu;

/**
 * Class LocaleEnNu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnNu extends LocaleEn {
	public function territory() {
		return new TerritoryNu;
	}
}
