<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDsb;

/**
 * Class LocaleDsb - Lower Sorbian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDsb extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'dolnoserbšćina';
	}

	public function endonymSortable() {
		return 'DOLNOSERBSCINA';
	}

	public function language() {
		return new LanguageDsb;
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
