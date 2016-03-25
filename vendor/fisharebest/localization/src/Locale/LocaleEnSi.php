<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySi;

/**
 * Class LocaleEnSi - English
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnSi extends LocaleEn {
	public function territory() {
		return new TerritorySi;
	}
}
