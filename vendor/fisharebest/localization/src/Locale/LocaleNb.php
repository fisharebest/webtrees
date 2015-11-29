<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNb;

/**
 * Class LocaleNb - Norwegian Bokmål
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNb extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'danish_ci';
	}

	public function endonym() {
		return 'norsk bokmål';
	}

	public function endonymSortable() {
		return 'NORSK BOKMAL';
	}

	public function language() {
		return new LanguageNb;
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
