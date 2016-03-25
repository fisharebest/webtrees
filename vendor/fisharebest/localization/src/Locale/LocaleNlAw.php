<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAw;

/**
 * Class LocaleNlAw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNlAw extends LocaleNl {
	public function territory() {
		return new TerritoryAw;
	}
}
