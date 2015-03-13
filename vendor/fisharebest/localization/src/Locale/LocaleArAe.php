<?php namespace Fisharebest\Localization;

/**
 * Class LocaleArAe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArAe extends LocaleAr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryAe;
	}
}
