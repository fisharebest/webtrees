<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSwc;

/**
 * Class LocaleSwc - Congo Swahili
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSwc extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'Kiswahili ya Kongo';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
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
