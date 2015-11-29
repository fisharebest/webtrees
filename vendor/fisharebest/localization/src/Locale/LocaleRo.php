<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRo;

/**
 * Class LocaleRo - Romanian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRo extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'romanian_ci';
	}

	public function endonym() {
		return 'română';
	}

	public function endonymSortable() {
		return 'ROMANA';
	}

	public function language() {
		return new LanguageRo;
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
