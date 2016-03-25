<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMl;

/**
 * Class LocaleMl - Malayalam
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMl extends AbstractLocale implements LocaleInterface {
	protected function digitsGroup() {
		return 2;
	}

	public function endonym() {
		return 'മലയാളം';
	}

	public function language() {
		return new LanguageMl;
	}
}
