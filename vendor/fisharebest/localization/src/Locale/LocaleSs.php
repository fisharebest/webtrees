<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSs - Swati
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSs extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Siswati';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'SISWATI';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSs;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
