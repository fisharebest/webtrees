<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageZu;

/**
 * Class LocaleZu - Zulu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleZu extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'isiZulu';
	}

	public function endonymSortable() {
		return 'ISIZULU';
	}

	public function language() {
		return new LanguageZu;
	}
}
