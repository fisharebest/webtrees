<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSs;

/**
 * Class LocaleSs - Swati
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSs extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Siswati';
	}

	public function endonymSortable() {
		return 'SISWATI';
	}

	public function language() {
		return new LanguageSs;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::NBSP,
			self::DECIMAL => self::COMMA,
		);
	}
}
