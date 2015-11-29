<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMd;

/**
 * Class LocaleRuMd
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRuMd extends LocaleRu {
	public function territory() {
		return new TerritoryMd;
	}
}
