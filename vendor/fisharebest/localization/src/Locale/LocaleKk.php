<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKk;

/**
 * Class LocaleKk - Kazakh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKk extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'қазақ тілі';
	}

	public function endonymSortable() {
		return 'ҚАЗАҚ ТІЛІ';
	}

	public function language() {
		return new LanguageKk;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
