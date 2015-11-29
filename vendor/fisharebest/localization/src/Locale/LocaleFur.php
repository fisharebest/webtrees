<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageFur;

/**
 * Class LocaleFur - Friulian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFur extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'furlan';
	}

	public function endonymSortable() {
		return 'FURLAN';
	}

	public function language() {
		return new LanguageFur;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::DOT,
			self::DECIMAL => self::COMMA,
		);
	}
}
