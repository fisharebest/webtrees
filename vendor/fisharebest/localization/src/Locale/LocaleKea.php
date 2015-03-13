<?php namespace Fisharebest\Localization;

/**
 * Class LocaleKea - Kabuverdianu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKea extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'kabuverdianu';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KABUVERDIANU';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKea;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
