<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIe;

/**
 * Class LocaleEnIe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnIe extends LocaleEn {
	public function territory() {
		return new TerritoryIe;
	}
}
