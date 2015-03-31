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
	/** {@inheritdoc} */
	public function variant() {
		return new VariantPosix;
	}
}
