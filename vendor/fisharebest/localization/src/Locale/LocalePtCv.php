<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCv;

/**
 * Class LocalePtCv
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePtCv extends LocalePt {
	public function territory() {
		return new TerritoryCv;
	}
}
