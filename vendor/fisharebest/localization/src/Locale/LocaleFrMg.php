<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMg;

/**
 * Class LocaleFrMg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrMg extends LocaleFr {
	public function territory() {
		return new TerritoryMg;
	}
}
