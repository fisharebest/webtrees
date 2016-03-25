<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryNe;

/**
 * Class LocaleHaNe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHaNe extends LocaleHa {
	public function territory() {
		return new TerritoryNe;
	}
}
