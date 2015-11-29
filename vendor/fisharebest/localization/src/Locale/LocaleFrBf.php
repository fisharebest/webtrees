<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBf;

/**
 * Class LocaleFrBf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrBf extends LocaleFr {
	public function territory() {
		return new TerritoryBf;
	}
}
