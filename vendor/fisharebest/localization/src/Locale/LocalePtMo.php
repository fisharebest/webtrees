<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMo;

/**
 * Class LocalePtMo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePtMo extends LocalePt {
	public function territory() {
		return new TerritoryMo;
	}
}
