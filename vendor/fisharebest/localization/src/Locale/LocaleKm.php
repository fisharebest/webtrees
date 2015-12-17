<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKm;

/**
 * Class LocaleKm - Khmer
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKm extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'ខ្មែរ';
	}

	public function language() {
		return new LanguageKm;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
