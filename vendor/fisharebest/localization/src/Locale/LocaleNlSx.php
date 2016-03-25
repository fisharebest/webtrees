<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySx;

/**
 * Class LocaleNlSx
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNlSx extends LocaleNl {
	public function territory() {
		return new TerritorySx;
	}
}
