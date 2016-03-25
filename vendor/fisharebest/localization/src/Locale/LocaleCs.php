<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageCs;

/**
 * Class LocaleCs - Czech
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleCs extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'croatian_ci';
	}

	public function endonym() {
		return 'čeština';
	}

	public function endonymSortable() {
		return 'CESTINA';
	}

	public function language() {
		return new LanguageCs;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}

	protected function percentFormat() {
		return '%s' . self::NBSP . self::PERCENT;
	}
}
