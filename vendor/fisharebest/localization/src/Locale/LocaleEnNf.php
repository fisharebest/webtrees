<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryNf;

/**
 * Class LocaleEnNf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnNf extends LocaleEn {
	public function territory() {
		return new TerritoryNf;
	}
}
