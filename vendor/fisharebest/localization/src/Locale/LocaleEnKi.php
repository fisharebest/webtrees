<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryKi;

/**
 * Class LocaleEnKi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnKi extends LocaleEn {
	public function territory() {
		return new TerritoryKi;
	}
}
