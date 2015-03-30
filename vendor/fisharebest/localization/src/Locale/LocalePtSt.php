<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySt;

/**
 * Class LocalePtSt
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePtSt extends LocalePt {
	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritorySt;
	}
}
