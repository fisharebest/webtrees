<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryLi;

/**
 * Class LocaleDeLi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDeLi extends LocaleDe {
	public function territory() {
		return new TerritoryLi;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::PRIME,
			self::DECIMAL => self::DOT,
		);
	}

	protected function percentFormat() {
		return '%s%%';
	}
}
