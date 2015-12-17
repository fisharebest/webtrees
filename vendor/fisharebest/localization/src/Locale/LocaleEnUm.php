<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryUm;

/**
 * Class LocaleEnUm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnUm extends LocaleEn {
	public function territory() {
		return new TerritoryUm;
	}
}
