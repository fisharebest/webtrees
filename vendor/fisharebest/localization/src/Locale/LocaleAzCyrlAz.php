<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAzCyrlAz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAzCyrlAz extends LocaleAzCyrl {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryAz;
	}
}
