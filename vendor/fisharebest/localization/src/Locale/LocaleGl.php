<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGl;

/**
 * Class LocaleGl - Galician
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGl extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'galego';
	}

	public function endonymSortable() {
		return 'GALEGO';
	}

	public function language() {
		return new LanguageGl;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
