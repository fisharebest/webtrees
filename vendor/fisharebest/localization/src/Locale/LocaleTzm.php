<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTzm;

/**
 * Class LocaleTzm - Central Atlas Tamazight
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTzm extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Tamaziɣt n laṭlaṣ';
	}

	public function endonymSortable() {
		return 'TAMAZIGHT N LATLAS';
	}

	public function language() {
		return new LanguageTzm;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
