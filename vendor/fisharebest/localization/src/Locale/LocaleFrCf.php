<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCf;

/**
 * Class LocaleFrCf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrCf extends LocaleFr {
	public function territory() {
		return new TerritoryCf;
	}
}
