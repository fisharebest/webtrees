<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySz;

/**
 * Class LocaleEnSz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnSz extends LocaleEn {
	public function territory() {
		return new TerritorySz;
	}
}
