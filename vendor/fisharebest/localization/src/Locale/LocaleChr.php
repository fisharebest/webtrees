<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageChr;

/**
 * Class LocaleChr - Cherokee
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleChr extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ᏣᎳᎩ';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageChr;
	}
}
