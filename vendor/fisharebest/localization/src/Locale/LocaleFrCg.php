<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCg;

/**
 * Class LocaleFrCg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrCg extends LocaleFr {
	public function territory() {
		return new TerritoryCg;
	}
}
