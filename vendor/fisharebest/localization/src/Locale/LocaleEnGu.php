<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGu;

/**
 * Class LocaleEnGu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnGu extends LocaleEn {
	public function territory() {
		return new TerritoryGu;
	}
}
