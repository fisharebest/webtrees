<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguagePl;

/**
 * Class LocalePl - Polish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePl extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'polish_ci';
	}

	public function endonym() {
		return 'polski';
	}

	public function endonymSortable() {
		return 'POLSKI';
	}

	public function language() {
		return new LanguagePl;
	}

	protected function minimumGroupingDigits() {
		return 2;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
