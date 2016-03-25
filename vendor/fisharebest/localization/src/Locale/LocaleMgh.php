<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMgh;

/**
 * Class LocaleMgh - Makhuwa-Meetto
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMgh extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Makua';
	}

	public function endonymSortable() {
		return 'MAKUA';
	}

	public function language() {
		return new LanguageMgh;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
