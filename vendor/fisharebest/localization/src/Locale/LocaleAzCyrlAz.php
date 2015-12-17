<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAz;

/**
 * Class LocaleAzCyrlAz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAzCyrlAz extends LocaleAzCyrl {
	public function territory() {
		return new TerritoryAz;
	}
}
