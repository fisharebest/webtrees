<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCh;

/**
 * Class LocaleEnCh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnCh extends LocaleEn {
	public function territory() {
		return new TerritoryCh;
	}
}
