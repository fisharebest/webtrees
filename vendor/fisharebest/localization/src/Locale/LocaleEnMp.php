<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMp;

/**
 * Class LocaleEnMp
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnMp extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMp;
	}
}
