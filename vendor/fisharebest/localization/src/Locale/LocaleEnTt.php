<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryTt;

/**
 * Class LocaleEnTt
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnTt extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryTt;
	}
}
