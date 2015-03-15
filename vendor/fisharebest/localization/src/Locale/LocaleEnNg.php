<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnNg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnNg extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryNg;
	}
}
