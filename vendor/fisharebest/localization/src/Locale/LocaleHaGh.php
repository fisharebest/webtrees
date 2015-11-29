<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGh;

/**
 * Class LocaleHaGh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHaGh extends LocaleHa {
	public function territory() {
		return new TerritoryGh;
	}
}
