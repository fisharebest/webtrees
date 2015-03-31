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
	/** {@inheritdoc} */
	protected function digitsGroup() {
		return 2;
	}

	/** {@inheritdoc} */
	public function endonym() {
		return 'हिंदी';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageHi;
	}
}
