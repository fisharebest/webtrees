<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGl;

/**
 * Class LocaleDaGl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDaGl extends LocaleDa {
	public function territory() {
		return new TerritoryGl;
	}
}
