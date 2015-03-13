<?php namespace Fisharebest\Localization;

/**
 * Class LocaleUk - Ukrainian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleUk extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'українська';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'УКРАЇНСЬКА';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageUk;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
