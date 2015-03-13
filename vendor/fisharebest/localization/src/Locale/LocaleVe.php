<?php namespace Fisharebest\Localization;

/**
 * Class LocaleVe - Venda
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleVe extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Tshivenḓa';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'TSHIVENDA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageVe;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
