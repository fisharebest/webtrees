<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryRw;

/**
 * Class LocaleFrRw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrRw extends LocaleFr {
	public function territory() {
		return new TerritoryRw;
	}
}
