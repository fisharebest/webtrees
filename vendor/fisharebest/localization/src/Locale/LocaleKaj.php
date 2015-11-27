<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKaj;

/**
 * Class LocaleKaj - Jju
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKaj extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Jju';
	}

	public function endonymSortable() {
		return 'JJU';
	}

	public function language() {
		return new LanguageKaj;
	}
}
