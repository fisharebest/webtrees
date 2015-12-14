<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageHe;

/**
 * Class LocaleHe - Hebrew
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleHe extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'עברית';
	}

	public function language() {
		return new LanguageHe;
	}

	public function numberSymbols() {
		return array(
			self::NEGATIVE => self::LTR_MARK . self::HYPHEN,
		);
	}
}
