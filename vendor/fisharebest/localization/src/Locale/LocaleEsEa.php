<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryEa;

/**
 * Class LocaleEsEa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsEa extends LocaleEs {
	public function territory() {
		return new TerritoryEa;
	}
}
