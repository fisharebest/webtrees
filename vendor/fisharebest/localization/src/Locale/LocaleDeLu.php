<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryLu;

/**
 * Class LocaleDeLu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDeLu extends LocaleDe {
	public function territory() {
		return new TerritoryLu;
	}
}
