<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnZm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnZm extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryZm;
	}
}
