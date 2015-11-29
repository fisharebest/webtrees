<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPw;

/**
 * Class LocaleEnPw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnPw extends LocaleEn {
	public function territory() {
		return new TerritoryPw;
	}
}
