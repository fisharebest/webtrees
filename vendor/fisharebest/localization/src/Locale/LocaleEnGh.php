<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGh;

/**
 * Class LocaleEnGh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnGh extends LocaleEn {
	public function territory() {
		return new TerritoryGh;
	}
}
