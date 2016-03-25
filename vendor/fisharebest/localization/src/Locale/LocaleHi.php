<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHi;

/**
 * Class LocaleHi - Hindi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHi extends AbstractLocale implements LocaleInterface {
	protected function digitsGroup() {
		return 2;
	}

	public function endonym() {
		return 'हिन्दी';
	}

	public function language() {
		return new LanguageHi;
	}
}
