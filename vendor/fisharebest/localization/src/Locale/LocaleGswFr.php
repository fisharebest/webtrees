<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryFr;

/**
 * Class LocaleGswFr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGswFr extends LocaleGsw {
	public function territory() {
		return new TerritoryFr;
	}
}
