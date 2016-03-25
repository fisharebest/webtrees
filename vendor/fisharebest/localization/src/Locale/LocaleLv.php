<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLv;

/**
 * Class LocaleLv - Latvian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLv extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'latvian_ci';
	}

	public function endonym() {
		return 'latviešu';
	}

	public function endonymSortable() {
		return 'LATVIESU';
	}

	public function language() {
		return new LanguageLv;
	}

	protected function minimumGroupingDigits() {
		return 3;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
