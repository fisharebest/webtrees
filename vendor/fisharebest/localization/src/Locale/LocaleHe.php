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
	/** {@inheritdoc} */
	public function endonym() {
		return 'עברית';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageHe;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::NEGATIVE => self::LTR_MARK . self::HYPHEN,
		);
	}
}
