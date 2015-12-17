<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryKz;

/**
 * Class LocaleRuKz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRuKz extends LocaleRu {
	public function territory() {
		return new TerritoryKz;
	}
}
