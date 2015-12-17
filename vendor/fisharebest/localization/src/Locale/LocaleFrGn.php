<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGn;

/**
 * Class LocaleFrGn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrGn extends LocaleFr {
	public function territory() {
		return new TerritoryGn;
	}
}
