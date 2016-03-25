<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageDv;

/**
 * Class LocaleDv - Divehi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleDv extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'ތާނަ';
	}

	public function language() {
		return new LanguageDv;
	}
}
