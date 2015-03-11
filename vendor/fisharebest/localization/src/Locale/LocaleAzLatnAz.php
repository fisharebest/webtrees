<?php namespace Fisharebest\Localization;

/**
 * Class LocaleAzLatnAz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleAzLatnAz extends LocaleAzLatn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryAz;
	}
}
