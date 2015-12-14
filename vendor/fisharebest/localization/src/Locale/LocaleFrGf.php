<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGf;

/**
 * Class LocaleFrGf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrGf extends LocaleFr {
	public function territory() {
		return new TerritoryGf;
	}
}
