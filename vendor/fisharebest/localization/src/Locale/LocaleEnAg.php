<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAg;

/**
 * Class LocaleEnAg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnAg extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryAg;
	}
}
