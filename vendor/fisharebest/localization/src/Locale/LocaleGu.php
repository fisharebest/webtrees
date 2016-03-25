<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageGu;

/**
 * Class LocaleGu - Gujarati
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleGu extends AbstractLocale implements LocaleInterface {
	protected function digitsGroup() {
		return 2;
	}

	public function endonym() {
		return 'ગુજરાતી';
	}

	public function language() {
		return new LanguageGu;
	}
}
