<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGv;

/**
 * Class LocaleGv - Manx
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGv extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Gaelg';
	}

	public function endonymSortable() {
		return 'GAELG';
	}

	public function language() {
		return new LanguageGv;
	}
}
