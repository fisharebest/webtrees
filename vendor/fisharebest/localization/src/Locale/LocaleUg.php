<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageUg;

/**
 * Class LocaleUg - Uyghur
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleUg extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'ئۇيغۇرچە';
	}

	public function language() {
		return new LanguageUg;
	}
}
