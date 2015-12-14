<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageOc;

/**
 * Class LocaleOc - Occitan
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleOc extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'lenga d’òc';
	}

	public function language() {
		return new LanguageOc;
	}
}
