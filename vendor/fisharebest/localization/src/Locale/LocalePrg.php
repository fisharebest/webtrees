<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguagePrg;

/**
 * Class LocalePrg - Old Prussian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePrg extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'latvian_ci';
	}

	public function endonym() {
		return 'prūsiskan';
	}

	public function endonymSortable() {
		return 'PRŪSISKAN';
	}

	public function language() {
		return new LanguagePrg;
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
