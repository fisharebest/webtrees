<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBa;

/**
 * Class LocaleSrLatnBa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSrLatnBa extends LocaleSrLatn {
	public function territory() {
		return new TerritoryBa;
	}
}
