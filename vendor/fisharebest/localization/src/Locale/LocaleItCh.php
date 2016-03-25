<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCh;

/**
 * Class LocaleItCh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleItCh extends LocaleIt {
	public function territory() {
		return new TerritoryCh;
	}

	public function numberSymbols() {
		return array(
			self::GROUP => self::PRIME,
		);
	}
}
