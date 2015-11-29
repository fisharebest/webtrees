<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHsb;

/**
 * Class LocaleHsb - Upper Sorbian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHsb extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'hornjoserbšćina';
	}

	public function endonymSortable() {
		return 'HORNJOSERBSCINA';
	}

	public function language() {
		return new LanguageHsb;
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
