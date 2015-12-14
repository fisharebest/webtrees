<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageIa;

/**
 * Class LocaleIa - Interlingua
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleIa extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'interlingua';
	}

	public function endonymSortable() {
		return 'INTERLINGUA';
	}

	public function language() {
		return new LanguageIa;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
