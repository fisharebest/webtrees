<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBl;

/**
 * Class LocaleFrBl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrBl extends LocaleFr {
	public function territory() {
		return new TerritoryBl;
	}
}
