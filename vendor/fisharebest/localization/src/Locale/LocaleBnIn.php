<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LocaleBnIn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleBnIn extends LocaleBn {
	public function territory() {
		return new TerritoryIn;
	}
}
