<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryJo;

/**
 * Class LocaleArJo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArJo extends LocaleAr {
	public function territory() {
		return new TerritoryJo;
	}
}
