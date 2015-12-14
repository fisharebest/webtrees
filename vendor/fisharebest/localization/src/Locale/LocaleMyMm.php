<?php namespace Fisharebest\Localization\Locale;

/**
 * Class LocaleMyMm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleMyMm extends LocaleMy {
	protected function minimumGroupingDigits() {
		return 3;
	}
}
