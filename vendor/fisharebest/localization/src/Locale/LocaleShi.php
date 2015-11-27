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
	public function direction() {
		return 'ltr';
	}

	public function endonym() {
		return 'ⵜⴰⵛⵍⵃⵉⵜ';
	}

	public function language() {
		return new LanguageShi;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
