<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnZw
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnZw extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryZw;
	}
}
