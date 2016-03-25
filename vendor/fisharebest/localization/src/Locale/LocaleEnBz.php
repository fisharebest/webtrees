<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBz;

/**
 * Class LocaleEnBz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnBz extends LocaleEn {
	public function territory() {
		return new TerritoryBz;
	}
}
