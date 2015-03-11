<?php namespace Fisharebest\Localization;

/**
 * Class LocaleRw - Kinyarwanda
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRw extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kinyarwanda';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KINYARWANDA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageRw;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
