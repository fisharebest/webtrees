<?php namespace Fisharebest\Localization;

/**
 * Class LocaleGl - Galician
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGl extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'galego';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'GALEGO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageGl;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
