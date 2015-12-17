<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBa;

/**
 * Class LocaleSrCyrlBa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSrCyrlBa extends LocaleSrCyrl {
	public function territory() {
		return new TerritoryBa;
	}
}
