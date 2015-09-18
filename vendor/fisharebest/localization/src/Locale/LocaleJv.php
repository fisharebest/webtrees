<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageJv;

/**
 * Class LocaleJv - Javanese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleJv extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ꦧꦱꦗꦮ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageJv;
	}
}
