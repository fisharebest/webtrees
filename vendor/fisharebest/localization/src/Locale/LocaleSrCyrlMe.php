<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMe;

/**
 * Class LocaleSrCyrlMe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSrCyrlMe extends LocaleSrCyrl {
	public function territory() {
		return new TerritoryMe;
	}
}
