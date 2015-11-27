<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMc;

/**
 * Class LocaleFrMc
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrMc extends LocaleFr {
	public function territory() {
		return new TerritoryMc;
	}
}
