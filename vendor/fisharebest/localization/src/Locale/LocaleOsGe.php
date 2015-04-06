<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGe;

/**
 * Class LocaleOsGe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleOsGe extends LocaleOs {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGe;
	}

	/** {@inheritdoc} */
	protected function minimumGroupingDigits() {
		return 1; // Georgia/Georgian uses 2
	}
}
