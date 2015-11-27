<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySh;

/**
 * Class LocaleEnSh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnSh extends LocaleEn {
	public function territory() {
		return new TerritorySh;
	}
}
