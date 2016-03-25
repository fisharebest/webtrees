<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRof;

/**
 * Class LocaleRof - Rombo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRof extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Kihorombo';
	}

	public function endonymSortable() {
		return 'KIHOROMBO';
	}

	public function language() {
		return new LanguageRof;
	}
}
