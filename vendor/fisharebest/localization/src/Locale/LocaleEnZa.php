<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryZa;

/**
 * Class LocaleEnZa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnZa extends LocaleEn {
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}

	public function territory() {
		return new TerritoryZa;
	}
}
