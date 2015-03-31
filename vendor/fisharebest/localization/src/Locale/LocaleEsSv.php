<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySv;

/**
 * Class LocaleEsSv
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsSv extends LocaleEs {
	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::COMMA,
			self::DECIMAL => self::DOT,
		);
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySv;
	}
}
