<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMe;

/**
 * Class LocaleSrLatnMe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSrLatnMe extends LocaleSrLatn {
	public function territory() {
		return new TerritoryMe;
	}
}
