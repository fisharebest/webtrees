<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDe;

/**
 * Class LocaleDe - German
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDe extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'german2_ci';
	}

	public function endonym() {
		return 'Deutsch';
	}

	public function endonymSortable() {
		return 'DEUTSCH';
	}

	public function language() {
		return new LanguageDe;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}

	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
