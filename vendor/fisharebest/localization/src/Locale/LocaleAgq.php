<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAgq - Aghem
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAgq extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Aghem';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'AGHEM';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageAgq;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
