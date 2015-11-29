<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTs;

/**
 * Class LocaleTs - Tsonga
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTs extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Xitsonga';
	}

	public function endonymSortable() {
		return 'XITSONGA';
	}

	public function language() {
		return new LanguageTs;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
