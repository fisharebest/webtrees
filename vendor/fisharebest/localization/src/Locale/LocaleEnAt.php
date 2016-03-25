<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAt;

/**
 * Class LocaleEnAt - English
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnAt extends LocaleEn {
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}

	public function territory() {
		return new TerritoryAt;
	}
}
