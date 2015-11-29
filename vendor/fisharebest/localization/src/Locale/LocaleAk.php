<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageAk;

/**
 * Class LocaleAk - Akan
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAk extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Akan';
	}

	public function endonymSortable() {
		return 'AKAN';
	}

	public function language() {
		return new LanguageAk;
	}
}
