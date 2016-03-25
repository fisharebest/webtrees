<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryTd;

/**
 * Class LocaleFrTd
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrTd extends LocaleFr {
	public function territory() {
		return new TerritoryTd;
	}
}
