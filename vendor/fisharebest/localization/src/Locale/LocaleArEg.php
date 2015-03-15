<?php namespace Fisharebest\Localization;

/**
 * Class LocaleArEg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleArEg extends LocaleAr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryEg;
	}
}
