<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryTg;

/**
 * Class LocaleFrTg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrTg extends LocaleFr {
	public function territory() {
		return new TerritoryTg;
	}
}
