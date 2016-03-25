<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMu;

/**
 * Class LocaleEnMu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnMu extends LocaleEn {
	public function territory() {
		return new TerritoryMu;
	}
}
