<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryKp;

/**
 * Class LocaleKoKp
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKoKp extends LocaleKo {
	public function territory() {
		return new TerritoryKp;
	}
}
