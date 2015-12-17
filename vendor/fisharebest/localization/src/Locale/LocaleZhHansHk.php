<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryHk;

/**
 * Class LocaleZhHansHk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleZhHansHk extends LocaleZhHans {
	public function territory() {
		return new TerritoryHk;
	}
}
