<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageOr;

/**
 * Class LocaleOr - Oriya
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleOr extends AbstractLocale implements LocaleInterface {
	protected function digitsGroup() {
		return 2;
	}

	public function endonym() {
		return 'ଓଡ଼ିଆ';
	}

	public function language() {
		return new LanguageOr;
	}
}
