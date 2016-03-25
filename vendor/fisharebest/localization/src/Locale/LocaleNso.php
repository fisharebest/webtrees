<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageNso;

/**
 * Class LocaleNso - Northern Sotho
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleNso extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Sesotho sa Leboa';
	}

	public function endonymSortable() {
		return 'SESOTHO SA LEBOA';
	}

	public function language() {
		return new LanguageNso;
	}

	public function numberSymbols() {
		return array(
			self::GROUP => self::NBSP,
		);
	}
}
