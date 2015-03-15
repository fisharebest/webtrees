<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSe - Northern Sami
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSe extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'davvisámegiella';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'DAVVISAMEGIELLA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSe;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP    => self::NBSP,
			self::DECIMAL  => self::COMMA,
			self::NEGATIVE => self::MINUS_SIGN,
		);
	}

	/** {@inheritdoc} */
	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
