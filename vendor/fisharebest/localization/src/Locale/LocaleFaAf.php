<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAf;

/**
 * Class LocaleFaAf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFaAf extends LocaleFa {
	public function territory() {
		return new TerritoryAf;
	}
}
