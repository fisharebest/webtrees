<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguagePa;

/**
 * Class LocalePa - Punjabi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocalePa extends AbstractLocale implements LocaleInterface {
	protected function digitsGroup() {
		return 2;
	}

	public function endonym() {
		return 'ਪੰਜਾਬੀ';
	}

	public function language() {
		return new LanguagePa;
	}
}
