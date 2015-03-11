<?php namespace Fisharebest\Localization;

/**
 * Class LocaleIt - Italian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleIt extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'italiano';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'ITALIANO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageIt;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
