<?php namespace Fisharebest\Localization;

/**
 * Class LocaleKl - Kalaallisut
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKl extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'kalaallisut';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KALAALLISUT';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKl;
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
