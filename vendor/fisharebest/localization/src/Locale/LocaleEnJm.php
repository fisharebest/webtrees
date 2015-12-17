<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryJm;

/**
 * Class LocaleEnJm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnJm extends LocaleEn {
	public function territory() {
		return new TerritoryJm;
	}
}
