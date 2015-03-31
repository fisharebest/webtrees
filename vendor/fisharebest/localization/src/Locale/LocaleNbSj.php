<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySj;

/**
 * Class LocaleNbSj
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNbSj extends LocaleNb {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySj;
	}
}
