<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryFr;

/**
 * Class LocaleIaFr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleIaFr extends LocaleIa {
	public function territory() {
		return new TerritoryFr;
	}
}
