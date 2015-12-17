<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryFi;

/**
 * Class LocaleEnDe - English
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnFi extends LocaleEn {
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
		);
	}

	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}

	public function territory() {
		return new TerritoryFi;
	}
}
