<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageWae;

/**
 * Class LocaleWae - Walser
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleWae extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Walser';
	}

	public function endonymSortable() {
		return 'WALSER';
	}

	public function language() {
		return new LanguageWae;
	}

	public function numberSymbols() {
		return array(
			self::GROUP   => self::APOSTROPHE,
			self::DECIMAL => self::COMMA,
		);
	}
}
