<?php namespace Fisharebest\Localization;

/**
 * Class LocaleTzm - Central Atlas Tamazight
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTzm extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Tamaziɣt';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'TAMAZIGHT';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageTzm;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
