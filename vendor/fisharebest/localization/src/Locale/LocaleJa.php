<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageJa;

/**
 * Class LocaleJa - Japanese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleJa extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return '日本語';
	}

	public function language() {
		return new LanguageJa;
	}
}
