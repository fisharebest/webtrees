<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LocaleTeoKe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTeoKe extends LocaleTeo {
	public function territory() {
		return new TerritoryKe;
	}
}
