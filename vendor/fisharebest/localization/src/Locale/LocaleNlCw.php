<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCw;

/**
 * Class LocaleNlCw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNlCw extends LocaleNl {
	public function territory() {
		return new TerritoryCw;
	}
}
