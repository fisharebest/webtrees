<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryHn;

/**
 * Class LocaleEsHn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsHn extends LocaleEs {
	public function territory() {
		return new TerritoryHn;
	}
}
