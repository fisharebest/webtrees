<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIr;

/**
 * Class LocaleCkbIr - Sorani
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCkbIr extends LocaleCkb {
	public function territory() {
		return new TerritoryIr;
	}
}
