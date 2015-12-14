<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFi;

/**
 * Class LocaleFi - Finnish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFi extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'suomi';
	}

	public function endonymSortable() {
		return 'SUOMI';
	}

	public function language() {
		return new LanguageFi;
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
