<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnGd
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnGd extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGd;
	}
}
