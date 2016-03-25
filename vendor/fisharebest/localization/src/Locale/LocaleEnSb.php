<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySb;

/**
 * Class LocaleEnSb
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnSb extends LocaleEn {
	public function territory() {
		return new TerritorySb;
	}
}
