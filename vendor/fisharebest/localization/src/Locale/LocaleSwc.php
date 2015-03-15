<?php namespace Fisharebest\Localization;

/**
 * Class LocaleSwc - Congo Swahili
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSwc extends Locale {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kiswahili ya Kongo';
	}

	/** {@inheritdoc} */
	protected function endonymSortable() {
		return 'KISWAHILI YA KONGO';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageSwc;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
