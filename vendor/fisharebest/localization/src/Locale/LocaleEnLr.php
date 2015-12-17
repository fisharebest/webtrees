<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryLr;

/**
 * Class LocaleEnLr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnLr extends LocaleEn {
	public function territory() {
		return new TerritoryLr;
	}
}
