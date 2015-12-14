<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNmg;

/**
 * Class LocaleNmg - Kwasio
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNmg extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Kwasio';
	}

	public function endonymSortable() {
		return 'KWASIO';
	}

	public function language() {
		return new LanguageNmg;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
