<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKhq;

/**
 * Class LocaleKhq - Koyra Chiini
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKhq extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Koyra ciini';
	}

	public function endonymSortable() {
		return 'KOYRA CIINI';
	}

	public function language() {
		return new LanguageKhq;
	}

	public function numberSymbols() {
		return array(
			self::GROUP => self::NBSP,
		);
	}
}
