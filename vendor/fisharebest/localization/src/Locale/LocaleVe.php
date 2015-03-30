<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageVe;

/**
 * Class LocaleVe - Venda
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleVe extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Tshivenḓa';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
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
