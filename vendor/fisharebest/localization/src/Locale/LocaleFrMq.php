<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMq;

/**
 * Class LocaleFrMq
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrMq extends LocaleFr {
	public function territory() {
		return new TerritoryMq;
	}
}
