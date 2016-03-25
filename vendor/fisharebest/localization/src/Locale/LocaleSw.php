<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSw;

/**
 * Class LocaleSw - Swahili
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSw extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Kiswahili';
	}

	public function endonymSortable() {
		return 'KISWAHILI';
	}

	public function language() {
		return new LanguageSw;
	}
}
