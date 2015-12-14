<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPm;

/**
 * Class LocaleFrPm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrPm extends LocaleFr {
	public function territory() {
		return new TerritoryPm;
	}
}
