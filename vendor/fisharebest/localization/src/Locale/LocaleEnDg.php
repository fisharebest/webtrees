<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryDg;

/**
 * Class LocaleEnDg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnDg extends LocaleEn {
	public function territory() {
		return new TerritoryDg;
	}
}
