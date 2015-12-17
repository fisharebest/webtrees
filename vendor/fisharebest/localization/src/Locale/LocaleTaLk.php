<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryLk;

/**
 * Class LocaleTaLk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTaLk extends LocaleTa {
	public function territory() {
		return new TerritoryLk;
	}
}
