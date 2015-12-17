<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySc;

/**
 * Class LocaleEnSc
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnSc extends LocaleEn {
	public function territory() {
		return new TerritorySc;
	}
}
