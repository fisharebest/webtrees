<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryYt;

/**
 * Class LocaleFrYt
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrYt extends LocaleFr {
	public function territory() {
		return new TerritoryYt;
	}
}
