<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageUr;

/**
 * Class LocaleUr - Urdu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleUr extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	protected function digitsGroup() {
		return 2;
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'اردو';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageUr;
	}

	/** {@inheritdoc} */
	public function numberSymbols() {
		return array(
			self::NEGATIVE => self::LTR_MARK . self::HYPHEN . self::LTR_MARK,
		);
	}
}
