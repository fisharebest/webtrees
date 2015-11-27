<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryLc;

/**
 * Class LocaleEnLc
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnLc extends LocaleEn {
	public function territory() {
		return new TerritoryLc;
	}
}
