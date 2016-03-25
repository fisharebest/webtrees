<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRw;

/**
 * Class LocaleRw - Kinyarwanda
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRw extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Kinyarwanda';
	}

	public function endonymSortable() {
		return 'KINYARWANDA';
	}

	public function language() {
		return new LanguageRw;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
