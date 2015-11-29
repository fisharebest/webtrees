<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCd;

/**
 * Class LocaleSwCd
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSwCd extends LocaleSw {
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}

	public function territory() {
		return new TerritoryCd;
	}
}
