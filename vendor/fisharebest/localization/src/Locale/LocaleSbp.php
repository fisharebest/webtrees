<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSbp;

/**
 * Class LocaleSbp - Sangu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSbp extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Ishisangu';
	}

	public function endonymSortable() {
		return 'ISHISANGU';
	}

	public function language() {
		return new LanguageSbp;
	}
}
