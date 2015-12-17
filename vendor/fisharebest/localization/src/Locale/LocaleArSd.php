<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySd;

/**
 * Class LocaleArSd
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArSd extends LocaleAr {
	public function territory() {
		return new TerritorySd;
	}
}
