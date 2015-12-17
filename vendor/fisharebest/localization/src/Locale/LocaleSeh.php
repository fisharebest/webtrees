<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSeh;

/**
 * Class LocaleSeh - Sena
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSeh extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'sena';
	}

	public function endonymSortable() {
		return 'SENA';
	}

	public function language() {
		return new LanguageSeh;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
