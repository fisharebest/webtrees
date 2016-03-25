<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIc;

/**
 * Class LocaleEsIc
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsIc extends LocaleEs {
	public function territory() {
		return new TerritoryIc;
	}
}
