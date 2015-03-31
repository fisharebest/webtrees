<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageKn;

/**
 * Class LocaleKn - Kannada
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleKn extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ಕನ್ನಡ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageKn;
	}
}
