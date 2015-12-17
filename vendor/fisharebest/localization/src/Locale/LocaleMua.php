<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMua;

/**
 * Class LocaleMua - Mundang
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMua extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'MUNDAŊ';
	}

	public function endonymSortable() {
		return 'MUNDAN';
	}

	public function language() {
		return new LanguageMua;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
