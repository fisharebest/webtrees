<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAx;

/**
 * Class LocaleSvAx
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSvAx extends LocaleSv {
	public function territory() {
		return new TerritoryAx;
	}
}
