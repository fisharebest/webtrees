<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMw;

/**
 * Class LocaleNyMw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNyMw extends LocaleNy {
	public function territory() {
		return new TerritoryMw;
	}
}
