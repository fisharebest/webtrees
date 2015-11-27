<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryZa;

/**
 * Class LocaleSsZa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSsZa extends LocaleSs {
	public function territory() {
		return new TerritoryZa;
	}
}
