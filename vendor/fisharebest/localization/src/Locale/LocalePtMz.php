<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMz;

/**
 * Class LocalePtMz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePtMz extends LocalePt {
	public function territory() {
		return new TerritoryMz;
	}
}
