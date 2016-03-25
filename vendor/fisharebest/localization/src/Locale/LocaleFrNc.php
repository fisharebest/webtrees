<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryNc;

/**
 * Class LocaleFrNc
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrNc extends LocaleFr {
	public function territory() {
		return new TerritoryNc;
	}
}
