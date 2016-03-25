<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySv;

/**
 * Class LocaleEsSv
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsSv extends LocaleEs {
	public function territory() {
		return new TerritorySv;
	}
}
