<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySs;

/**
 * Class LocaleArSs
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArSs extends LocaleAr {
	public function territory() {
		return new TerritorySs;
	}
}
