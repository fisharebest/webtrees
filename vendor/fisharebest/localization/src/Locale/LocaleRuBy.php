<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBy;

/**
 * Class LocaleRuBy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRuBy extends LocaleRu {
	public function territory() {
		return new TerritoryBy;
	}
}
