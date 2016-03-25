<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGa;

/**
 * Class LocaleFrGa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrGa extends LocaleFr {
	public function territory() {
		return new TerritoryGa;
	}
}
