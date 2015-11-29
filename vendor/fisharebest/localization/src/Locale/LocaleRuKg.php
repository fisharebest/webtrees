<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryKg;

/**
 * Class LocaleRuKg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRuKg extends LocaleRu {
	public function territory() {
		return new TerritoryKg;
	}
}
