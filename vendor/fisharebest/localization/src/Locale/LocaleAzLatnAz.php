<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAz;

/**
 * Class LocaleAzLatnAz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAzLatnAz extends LocaleAzLatn {
	public function territory() {
		return new TerritoryAz;
	}
}
