<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCr;

/**
 * Class LocaleEsCr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsCr extends LocaleEs {
	public function numberSymbols() {
		return array(
				self::GROUP   => self::NBSP,
				self::DECIMAL => self::COMMA,
		);
	}

	public function territory() {
		return new TerritoryCr;
	}
}
