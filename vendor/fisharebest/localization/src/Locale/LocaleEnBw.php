<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnBw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnBw extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryBw;
	}
}
