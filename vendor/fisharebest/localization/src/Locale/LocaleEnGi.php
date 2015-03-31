<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGi;

/**
 * Class LocaleEnGi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnGi extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGi;
	}
}
