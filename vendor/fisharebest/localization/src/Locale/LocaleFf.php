<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFf;

/**
 * Class LocaleFf - Fulah
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFf extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Pulaar';
	}

	public function endonymSortable() {
		return 'PULAAR';
	}

	public function language() {
		return new LanguageFf;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
