<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBe;

/**
 * Class LocaleDeBe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDeBe extends LocaleDe {
	public function territory() {
		return new TerritoryBe;
	}
}
