<?php namespace Fisharebest\Localization;

/**
 * Class LocaleArEh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArEh extends LocaleAr {
	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP    => self::COMMA,
			self::DECIMAL  => self::DOT,
			self::NEGATIVE => self::LTR_MARK . self::HYPHEN,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s%%';
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryEh;
	}
}
