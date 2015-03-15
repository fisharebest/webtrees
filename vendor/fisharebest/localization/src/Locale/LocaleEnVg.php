<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnVg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnVg extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryVg;
	}
}
