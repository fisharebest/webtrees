<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryTv;

/**
 * Class LocaleEnTv
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnTv extends LocaleEn {
	public function territory() {
		return new TerritoryTv;
	}
}
