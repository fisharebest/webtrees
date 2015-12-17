<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySo;

/**
 * Class LocaleArSo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArSo extends LocaleAr {
	public function territory() {
		return new TerritorySo;
	}
}
