<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageTi;

/**
 * Class LocaleTi - Tigrinya
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTi extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'ትግርኛ';
	}

	public function language() {
		return new LanguageTi;
	}
}
