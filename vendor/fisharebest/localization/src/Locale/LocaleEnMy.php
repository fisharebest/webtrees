<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnMy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnMy extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMy;
	}
}
