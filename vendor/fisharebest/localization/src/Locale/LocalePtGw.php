<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGw;

/**
 * Class LocalePtGw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePtGw extends LocalePt {
	public function territory() {
		return new TerritoryGw;
	}
}
