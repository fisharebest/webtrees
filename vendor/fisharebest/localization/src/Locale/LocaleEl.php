<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEl;

/**
 * Class LocaleEl - Greek
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEl extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Ελληνικά';
	}

	public function endonymSortable() {
		return 'ΕΛΛΗΝΙΚΆ';
	}

	public function language() {
		return new LanguageEl;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
