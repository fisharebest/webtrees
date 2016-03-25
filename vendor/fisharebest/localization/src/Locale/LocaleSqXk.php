<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryXk;

/**
 * Class LocaleSqXk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSqXk extends LocaleSq {
	public function territory() {
		return new TerritoryXk;
	}
}
