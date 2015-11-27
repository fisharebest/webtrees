<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMn;

/**
 * Class LocaleMn - Mongolian
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMn extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'монгол';
	}

	public function endonymSortable() {
		return 'МОНГОЛ';
	}

	public function language() {
		return new LanguageMn;
	}
}
