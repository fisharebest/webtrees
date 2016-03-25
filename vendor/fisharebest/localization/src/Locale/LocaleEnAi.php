<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAi;

/**
 * Class LocaleEnAi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnAi extends LocaleEn {
	public function territory() {
		return new TerritoryAi;
	}
}
