<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySa;

/**
 * Class LocaleArSa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArSa extends LocaleAr {
	public function territory() {
		return new TerritorySa;
	}
}
