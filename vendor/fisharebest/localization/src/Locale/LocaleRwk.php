<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageRwk;

/**
 * Class LocaleRwk - Rwa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleRwk extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Kiruwa';
	}

	public function endonymSortable() {
		return 'KIRUWA';
	}

	public function language() {
		return new LanguageRwk;
	}
}
