<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryJe;

/**
 * Class LocaleEnJe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnJe extends LocaleEn {
	public function territory() {
		return new TerritoryJe;
	}
}
