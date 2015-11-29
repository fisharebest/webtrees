<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEn;

/**
 * Class LocaleEn - English
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEn extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'English';
	}

	public function endonymSortable() {
		return 'ENGLISH';
	}

	public function language() {
		return new LanguageEn;
	}
}
