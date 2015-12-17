<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBj;

/**
 * Class LocaleFrBj
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrBj extends LocaleFr {
	public function territory() {
		return new TerritoryBj;
	}
}
