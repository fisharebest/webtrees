<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBq;

/**
 * Class LocaleNlBq
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNlBq extends LocaleNl {
	public function territory() {
		return new TerritoryBq;
	}
}
