<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIt;

/**
 * Class LocaleIt - Italian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleIt extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'italiano';
	}

	public function endonymSortable() {
		return 'ITALIANO';
	}

	public function language() {
		return new LanguageIt;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
