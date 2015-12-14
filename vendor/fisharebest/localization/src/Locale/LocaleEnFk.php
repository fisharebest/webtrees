<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryFk;

/**
 * Class LocaleEnFk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnFk extends LocaleEn {
	public function territory() {
		return new TerritoryFk;
	}
}
