<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryZw;

/**
 * Class LocaleStZw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleStZw extends LocaleSt {
	public function territory() {
		return new TerritoryZw;
	}
}
