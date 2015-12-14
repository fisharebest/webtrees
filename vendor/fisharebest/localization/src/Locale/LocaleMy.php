<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Language\LanguageMy;

/**
 * Class LocaleMy - Burmese
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMy extends AbstractLocale implements LocaleInterface {
	public function endonym() {
		return 'ဗမာ';
	}

	public function language() {
		return new LanguageMy;
	}

	protected function minimumGroupingDigits() {
		return 3;
	}
}
