<?php namespace Fisharebest\Localization;

/**
 * Class LocaleArKm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArKm extends LocaleAr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryKm;
	}
}
