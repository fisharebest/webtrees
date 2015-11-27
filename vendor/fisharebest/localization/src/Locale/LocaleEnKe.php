<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LocaleEnKe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnKe extends LocaleEn {
	public function territory() {
		return new TerritoryKe;
	}
}
