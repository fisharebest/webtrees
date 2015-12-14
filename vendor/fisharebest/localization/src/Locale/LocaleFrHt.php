<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryHt;

/**
 * Class LocaleFrHt
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrHt extends LocaleFr {
	public function territory() {
		return new TerritoryHt;
	}
}
