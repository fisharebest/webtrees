<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryUg;

/**
 * Class LocaleSwUg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSwUg extends LocaleSw {
	public function territory() {
		return new TerritoryUg;
	}
}
