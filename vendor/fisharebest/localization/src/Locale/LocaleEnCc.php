<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCc;

/**
 * Class LocaleEnCc
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnCc extends LocaleEn {
	public function territory() {
		return new TerritoryCc;
	}
}
