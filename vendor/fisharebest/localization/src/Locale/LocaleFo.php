<?php namespace Fisharebest\Localization;

/**
 * Class LocaleFo - Faroese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFo extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'føroyskt';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'FOROYSKT';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageFo;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP    => self::DOT,
			self::DECIMAL  => self::COMMA,
			self::NEGATIVE => self::MINUS_SIGN,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
