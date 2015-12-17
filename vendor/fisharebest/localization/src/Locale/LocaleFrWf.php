<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryWf;

/**
 * Class LocaleFrWf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrWf extends LocaleFr {
	public function territory() {
		return new TerritoryWf;
	}
}
