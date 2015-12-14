<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKsh;

/**
 * Class LocaleKsh - Colognian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKsh extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Kölsch';
	}

	public function endonymSortable() {
		return 'KOLSCH';
	}

	public function language() {
		return new LanguageKsh;
	}

	public function numberSymbols() {
		return array(
			self::GROUP    => self::NBSP,
			self::DECIMAL  => self::COMMA,
			self::NEGATIVE => self::MINUS_SIGN,
		);
	}

	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
