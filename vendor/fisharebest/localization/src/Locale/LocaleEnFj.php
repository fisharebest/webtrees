<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryFj;

/**
 * Class LocaleEnFj
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnFj extends LocaleEn {
	public function territory() {
		return new TerritoryFj;
	}
}
