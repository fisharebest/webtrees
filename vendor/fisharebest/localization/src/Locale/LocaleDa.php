<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDa;

/**
 * Class LocaleDa - Danish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDa extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'danish_ci';
	}

	public function endonym() {
		return 'dansk';
	}

	public function endonymSortable() {
		return 'DANSK';
	}

	public function language() {
		return new LanguageDa;
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
