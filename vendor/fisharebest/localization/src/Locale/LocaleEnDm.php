<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryDm;

/**
 * Class LocaleEnDm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnDm extends LocaleEn {
	public function territory() {
		return new TerritoryDm;
	}
}
