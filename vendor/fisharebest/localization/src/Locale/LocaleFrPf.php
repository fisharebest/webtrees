<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPf;

/**
 * Class LocaleFrPf
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrPf extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryPf;
	}
}
