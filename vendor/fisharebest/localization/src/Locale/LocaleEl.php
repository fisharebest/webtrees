<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEl - Greek
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEl extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Ελληνικά';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'ΕΛΛΗΝΙΚΆ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageEl;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
