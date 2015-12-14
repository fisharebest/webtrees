<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNr;

/**
 * Class LocaleNr - South Ndebele
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNr extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'isiNdebele';
	}

	public function endonymSortable() {
		return 'ISINDEBELE';
	}

	public function language() {
		return new LanguageNr;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
