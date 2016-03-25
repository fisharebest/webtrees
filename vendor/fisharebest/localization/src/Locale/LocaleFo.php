<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFo;

/**
 * Class LocaleFo - Faroese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFo extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'føroyskt';
	}

	public function endonymSortable() {
		return 'FOROYSKT';
	}

	public function language() {
		return new LanguageFo;
	}

	public function numberSymbols() {
		return array(
			self::GROUP    => self::DOT,
			self::DECIMAL  => self::COMMA,
			self::NEGATIVE => self::MINUS_SIGN,
		);
	}

	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
