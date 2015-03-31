<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryOm;

/**
 * Class LocaleArOm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArOm extends LocaleAr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryOm;
	}
}
