<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageVi;

/**
 * Class LocaleVi - Vietnamese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleVi extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'vietnamese_ci';
	}

	public function endonym() {
		return 'Tiếng Việt';
	}

	public function endonymSortable() {
		return 'TIENG VIET';
	}

	public function language() {
		return new LanguageVi;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
