<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMs;

/**
 * Class LocaleEnMs
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnMs extends LocaleEn {
	public function territory() {
		return new TerritoryMs;
	}
}
