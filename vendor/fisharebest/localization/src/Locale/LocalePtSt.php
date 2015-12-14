<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySt;

/**
 * Class LocalePtSt
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePtSt extends LocalePt {
	public function territory() {
		return new TerritorySt;
	}
}
