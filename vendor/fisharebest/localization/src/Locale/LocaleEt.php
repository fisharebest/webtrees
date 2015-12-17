<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEt;

/**
 * Class LocaleEt - Estonian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEt extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'estonian_ci';
	}

	public function endonym() {
		return 'eesti';
	}

	public function endonymSortable() {
		return 'EESTI';
	}

	public function language() {
		return new LanguageEt;
	}

	protected function minimumGroupingDigits() {
		return 3;
	}

	public function numberSymbols() {
		return array(
			self::GROUP    => self::NBSP,
			self::DECIMAL  => self::COMMA,
			self::NEGATIVE => self::MINUS_SIGN,
		);
	}
}
