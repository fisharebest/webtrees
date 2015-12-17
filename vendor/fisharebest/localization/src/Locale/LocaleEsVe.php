<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryVe;

/**
 * Class LocaleEsVe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsVe extends LocaleEs {
	public function territory() {
		return new TerritoryVe;
	}
}
