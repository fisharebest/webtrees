<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryFi;

/**
 * Class LocaleSeFi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSeFi extends LocaleSe {
	public function territory() {
		return new TerritoryFi;
	}
}
