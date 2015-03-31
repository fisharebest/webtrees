<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryDz;

/**
 * Class LocaleArDz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArDz extends LocaleAr {
	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP    => self::DOT,
			self::DECIMAL  => self::COMMA,
			self::NEGATIVE => self::LTR_MARK . self::HYPHEN,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s%%';
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryDz;
	}
}
