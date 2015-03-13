<?php namespace Fisharebest\Localization;

/**
 * Class LocaleDyo - Jola-Fonyi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDyo extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'joola';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'JOOLA';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageDyo;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
