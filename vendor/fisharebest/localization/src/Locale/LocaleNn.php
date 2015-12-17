<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNn;

/**
 * Class LocaleNn - Norwegian Nynorsk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNn extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'danish_ci';
	}

	public function endonym() {
		return 'nynorsk';
	}

	public function endonymSortable() {
		return 'NYNORSK';
	}

	public function language() {
		return new LanguageNn;
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
