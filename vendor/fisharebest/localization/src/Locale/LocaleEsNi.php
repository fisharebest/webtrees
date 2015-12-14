<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryNi;

/**
 * Class LocaleEsNi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsNi extends LocaleEs {
	public function territory() {
		return new TerritoryNi;
	}
}
