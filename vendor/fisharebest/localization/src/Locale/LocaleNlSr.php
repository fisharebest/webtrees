<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySr;

/**
 * Class LocaleNlSr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNlSr extends LocaleNl {
	public function territory() {
		return new TerritorySr;
	}
}
