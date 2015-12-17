<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCl;

/**
 * Class LocaleEsCl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsCl extends LocaleEs {
	public function territory() {
		return new TerritoryCl;
	}
}
