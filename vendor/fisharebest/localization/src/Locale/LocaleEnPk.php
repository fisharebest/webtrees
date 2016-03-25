<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPk;

/**
 * Class LocaleEnPk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnPk extends LocaleEn {
	protected function digitsGroup() {
		return 2;
	}

	public function territory() {
		return new TerritoryPk;
	}
}
