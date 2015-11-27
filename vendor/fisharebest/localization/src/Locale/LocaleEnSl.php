<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySl;

/**
 * Class LocaleEnSl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnSl extends LocaleEn {
	public function territory() {
		return new TerritorySl;
	}
}
