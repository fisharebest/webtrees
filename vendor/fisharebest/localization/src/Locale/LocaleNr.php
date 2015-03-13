<?php namespace Fisharebest\Localization;

/**
 * Class LocaleNr - South Ndebele
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNr extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'isiNdebele';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'ISINDEBELE';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageNr;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
