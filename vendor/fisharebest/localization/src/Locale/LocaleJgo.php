<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageJgo;

/**
 * Class LocaleJgo - Ngomba
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleJgo extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Ndaꞌa';
	}

	public function endonymSortable() {
		return 'NDAA';
	}

	public function language() {
		return new LanguageJgo;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
