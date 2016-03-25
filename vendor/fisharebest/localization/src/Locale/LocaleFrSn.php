<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySn;

/**
 * Class LocaleFrSn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrSn extends LocaleFr {
	public function territory() {
		return new TerritorySn;
	}
}
