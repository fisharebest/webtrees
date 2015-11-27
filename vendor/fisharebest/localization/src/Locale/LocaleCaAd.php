<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAd;

/**
 * Class LocaleCaAd
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCaAd extends LocaleCa {
	public function territory() {
		return new TerritoryAd;
	}
}
