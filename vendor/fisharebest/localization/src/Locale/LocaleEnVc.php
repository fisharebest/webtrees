<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnVc
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnVc extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryVc;
	}
}
