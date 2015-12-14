<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageSyr;

/**
 * Class LocaleSyr - Syriac
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSyr extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'Syriac';
	}

	public function language() {
		return new LanguageSyr;
	}
}
