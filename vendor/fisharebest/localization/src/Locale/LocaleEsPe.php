<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPe;

/**
 * Class LocaleEsPe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsPe extends LocaleEs {
	public function territory() {
		return new TerritoryPe;
	}
}
