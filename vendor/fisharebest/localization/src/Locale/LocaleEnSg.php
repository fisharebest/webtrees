<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySg;

/**
 * Class LocaleEnSg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnSg extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySg;
	}
}
