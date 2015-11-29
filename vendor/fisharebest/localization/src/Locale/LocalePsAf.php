<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAf;

/**
 * Class LocalePsAf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePsAf extends LocalePs {
	public function territory() {
		return new TerritoryAf;
	}
}
