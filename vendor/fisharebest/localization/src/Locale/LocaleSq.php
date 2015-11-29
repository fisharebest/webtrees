<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSq;

/**
 * Class LocaleSq - Albanian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSq extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'shqip';
	}

	public function endonymSortable() {
		return 'SHQIP';
	}

	public function language() {
		return new LanguageSq;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
