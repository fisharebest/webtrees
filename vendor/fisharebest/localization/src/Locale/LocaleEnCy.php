<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCy;

/**
 * Class LocaleEnCy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnCy extends LocaleEn {
	public function territory() {
		return new TerritoryCy;
	}
}
