<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnVi
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnVi extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryVi;
	}
}
