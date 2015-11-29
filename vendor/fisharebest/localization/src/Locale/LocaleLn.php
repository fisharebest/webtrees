<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageLn;

/**
 * Class LocaleLn - Lingala
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleLn extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'lingála';
	}

	public function endonymSortable() {
		return 'LINGALA';
	}

	public function language() {
		return new LanguageLn;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
