<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMk;

/**
 * Class LocaleSqMk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSqMk extends LocaleSq {
	public function territory() {
		return new TerritoryMk;
	}
}
