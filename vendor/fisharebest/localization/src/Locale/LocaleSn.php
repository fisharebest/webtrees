<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSn;

/**
 * Class LocaleSn - Shona
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSn extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'chiShona';
	}

	public function endonymSortable() {
		return 'CHISHONA';
	}

	public function language() {
		return new LanguageSn;
	}
}
