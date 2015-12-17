<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMf;

/**
 * Class LocaleFrMf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrMf extends LocaleFr {
	public function territory() {
		return new TerritoryMf;
	}
}
