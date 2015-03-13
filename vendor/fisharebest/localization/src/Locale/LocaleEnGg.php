<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnGg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnGg extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGg;
	}
}
