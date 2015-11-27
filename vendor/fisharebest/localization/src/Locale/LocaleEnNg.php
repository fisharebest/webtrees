<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryNg;

/**
 * Class LocaleEnNg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnNg extends LocaleEn {
	public function territory() {
		return new TerritoryNg;
	}
}
