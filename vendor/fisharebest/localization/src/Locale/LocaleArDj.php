<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryDj;

/**
 * Class LocaleArDj
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArDj extends LocaleAr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryDj;
	}
}
