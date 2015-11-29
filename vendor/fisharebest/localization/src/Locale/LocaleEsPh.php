<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPh;

/**
 * Class LocaleEsPh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsPh extends LocaleEs {
	public function territory() {
		return new TerritoryPh;
	}
}
