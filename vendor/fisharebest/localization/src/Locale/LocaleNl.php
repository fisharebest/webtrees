<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNl;

/**
 * Class LocaleNl - Dutch
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNl extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Nederlands';
	}

	public function endonymSortable() {
		return 'NEDERLANDS';
	}

	public function language() {
		return new LanguageNl;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
