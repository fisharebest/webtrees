<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHa;

/**
 * Class LocaleHa - Hausa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHa extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Hausa';
	}

	public function endonymSortable() {
		return 'HAUSA';
	}

	public function language() {
		return new LanguageHa;
	}
}
