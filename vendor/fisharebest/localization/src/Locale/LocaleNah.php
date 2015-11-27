<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNah;

/**
 * Class LocaleNah - Nahuatl
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNah extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Nahuatlahtolli';
	}

	public function endonymSortable() {
		return 'NAHUATLAHTOLLI';
	}

	public function language() {
		return new LanguageNah;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::COMMA,
			self::DECIMAL => self::DOT,
		);
	}

	protected function percentFormat() {
		return '%s' . self::PERCENT;
	}

}
