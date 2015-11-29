<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryZa;

/**
 * Class LocaleTnZa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTnZa extends LocaleTn {
	public function territory() {
		return new TerritoryZa;
	}
}
