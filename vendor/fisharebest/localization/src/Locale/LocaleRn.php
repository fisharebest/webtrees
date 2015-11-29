<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRn;

/**
 * Class LocaleRn - Rundi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRn extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Ikirundi';
	}

	public function endonymSortable() {
		return 'IKIRUNDI';
	}

	public function language() {
		return new LanguageRn;
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
