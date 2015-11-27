<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCk;

/**
 * Class LocaleEnCk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnCk extends LocaleEn {
	public function territory() {
		return new TerritoryCk;
	}
}
