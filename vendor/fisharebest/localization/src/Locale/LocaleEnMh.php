<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMh;

/**
 * Class LocaleEnMh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnMh extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMh;
	}
}
