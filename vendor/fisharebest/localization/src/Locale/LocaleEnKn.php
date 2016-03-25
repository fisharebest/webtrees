<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryKn;

/**
 * Class LocaleEnKn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnKn extends LocaleEn {
	public function territory() {
		return new TerritoryKn;
	}
}
