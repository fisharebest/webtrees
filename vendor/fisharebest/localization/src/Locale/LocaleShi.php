<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageShi;

/**
 * Class LocaleShi - Tachelhit
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleShi extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function direction() {
		return 'ltr';
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'ⵜⴰⵎⴰⵣⵉⵖⵜ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageShi;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
