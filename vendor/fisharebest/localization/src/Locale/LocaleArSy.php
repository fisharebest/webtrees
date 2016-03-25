<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySy;

/**
 * Class LocaleArSy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArSy extends LocaleAr {
	public function territory() {
		return new TerritorySy;
	}
}
