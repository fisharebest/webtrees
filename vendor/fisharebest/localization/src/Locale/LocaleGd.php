<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGd;

/**
 * Class LocaleGd - Scottish Gaelic
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGd extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Gàidhlig';
	}

	public function endonymSortable() {
		return 'GAIDHLIG';
	}

	public function language() {
		return new LanguageGd;
	}
}
