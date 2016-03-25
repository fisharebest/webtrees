<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHr;

/**
 * Class LocaleHr - Croatian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHr extends AbstractLocale implements LocaleInterface {
	public function collation() {
		return 'croatian_ci';
	}

	public function endonym() {
		return 'hrvatski';
	}

	public function endonymSortable() {
		return 'HRVATSKI';
	}

	public function language() {
		return new LanguageHr;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
