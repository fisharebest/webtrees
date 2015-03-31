<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryVu;

/**
 * Class LocaleFrVu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrVu extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryVu;
	}
}
