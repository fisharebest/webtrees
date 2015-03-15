<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnNa
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnNa extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryNa;
	}
}
