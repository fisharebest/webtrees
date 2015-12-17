<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySg;

/**
 * Class LocaleMsSg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMsSg extends LocaleMs {
	public function territory() {
		return new TerritorySg;
	}
}
