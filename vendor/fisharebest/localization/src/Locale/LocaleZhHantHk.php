<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryHk;

/**
 * Class LocaleZhHantHk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleZhHantHk extends LocaleZhHant {
	public function territory() {
		return new TerritoryHk;
	}
}
