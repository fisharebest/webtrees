<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLo;

/**
 * Class LocaleLo - Lao
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLo extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'ລາວ';
	}

	public function language() {
		return new LanguageLo;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
