<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageYi;

/**
 * Class LocaleYi - Yiddish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleYi extends AbstractLocale implements LocaleInterface {
	/** {@inheritdoc} */
	public function endonym() {
		return 'ייִדיש';
	}

	/** {@inheritdoc} */
	public function language() {
		return new LanguageYi;
	}
}
