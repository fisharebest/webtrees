<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCf;

/**
 * Class LocaleLnCf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLnCf extends LocaleLn {
	public function territory() {
		return new TerritoryCf;
	}
}
