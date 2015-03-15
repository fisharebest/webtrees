<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnFm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnFm extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryFm;
	}
}
