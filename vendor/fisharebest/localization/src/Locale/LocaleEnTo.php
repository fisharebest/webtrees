<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryTo;

/**
 * Class LocaleEnTo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnTo extends LocaleEn {
	public function territory() {
		return new TerritoryTo;
	}
}
