<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnGy
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnGy extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGy;
	}
}
