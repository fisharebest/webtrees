<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPr;

/**
 * Class LocaleEnPr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnPr extends LocaleEn {
	public function territory() {
		return new TerritoryPr;
	}
}
