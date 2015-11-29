<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFy;

/**
 * Class LocaleFy - Western Frisian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFy extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'West-Frysk';
	}

	public function endonymSortable() {
		return 'WEST FRYSK';
	}

	public function language() {
		return new LanguageFy;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
