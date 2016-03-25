<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryTg;

/**
 * Class LocaleEeTg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEeTg extends LocaleEe {
	public function territory() {
		return new TerritoryTg;
	}
}
