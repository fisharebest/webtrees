<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySm;

/**
 * Class LocaleItSm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleItSm extends LocaleIt {
	public function territory() {
		return new TerritorySm;
	}
}
