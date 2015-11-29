<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageYav;

/**
 * Class LocaleYav - Yangben
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleYav extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'nuasue';
	}

	public function endonymSortable() {
		return 'NUASUE';
	}

	public function language() {
		return new LanguageYav;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
