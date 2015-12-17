<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryRe;

/**
 * Class LocaleFrRe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrRe extends LocaleFr {
	public function territory() {
		return new TerritoryRe;
	}
}
