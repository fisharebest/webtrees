<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBn;

/**
 * Class LocaleMsMy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMsBn extends LocaleMs {
	public function numberSymbols() {
		return array(
				self::GROUP   => self::DOT,
				self::DECIMAL => self::COMMA,
		);
	}

	public function territory() {
		return new TerritoryBn;
	}
}
