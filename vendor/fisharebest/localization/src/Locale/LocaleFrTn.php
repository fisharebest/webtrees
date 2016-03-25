<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryTn;

/**
 * Class LocaleFrTn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrTn extends LocaleFr {
	public function territory() {
		return new TerritoryTn;
	}
}
