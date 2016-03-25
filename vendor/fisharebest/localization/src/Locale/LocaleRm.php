<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRm;

/**
 * Class LocaleRm - Romansh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRm extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'rumantsch';
	}

	public function endonymSortable() {
		return 'RUMANTSCH';
	}

	public function language() {
		return new LanguageRm;
	}

	public function numberSymbols() {
		return array(
			self::GROUP    => self::APOSTROPHE,
			self::NEGATIVE => self::MINUS_SIGN,
		);
	}

	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
