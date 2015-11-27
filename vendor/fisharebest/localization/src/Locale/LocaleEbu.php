<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageEbu;

/**
 * Class LocaleEbu - Embu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEbu extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Kĩembu';
	}

	public function endonymSortable() {
		return 'KIEMBU';
	}

	public function language() {
		return new LanguageEbu;
	}
}
