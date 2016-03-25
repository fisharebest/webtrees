<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Variant\VariantPosix;

/**
 * Class LocaleEnUsPosix
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnUsPosix extends LocaleEnUs {
	public function numberSymbols() {
		return array(
				self::GROUP => '',
		);
	}

	public function variant() {
		return new VariantPosix;
	}
}
