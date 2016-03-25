<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCu;

/**
 * Class LocaleEsCu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsCu extends LocaleEs {
	public function territory() {
		return new TerritoryCu;
	}
}
