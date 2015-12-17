<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDyo;

/**
 * Class LocaleDyo - Jola-Fonyi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDyo extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'joola';
	}

	public function endonymSortable() {
		return 'JOOLA';
	}

	public function language() {
		return new LanguageDyo;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
