<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMr;

/**
 * Class LocaleMr - Marathi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMr extends AbstractLocale implements LocaleInterface {
	protected function digitsGroup() {
		return 2;
	}

	public function endonym() {
		return 'मराठी';
	}

	public function language() {
		return new LanguageMr;
	}
}
