<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryLu;

/**
 * Class LocaleFrLu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrLu extends LocaleFr {
	public function territory() {
		return new TerritoryLu;
	}

	public function numberSymbols() {
		return array(
			self::DECIMAL => self::COMMA,
			self::GROUP   => self::DOT,
		);
	}
}
