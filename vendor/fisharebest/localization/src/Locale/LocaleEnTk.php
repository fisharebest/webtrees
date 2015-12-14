<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryTk;

/**
 * Class LocaleEnTk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnTk extends LocaleEn {
	public function territory() {
		return new TerritoryTk;
	}
}
