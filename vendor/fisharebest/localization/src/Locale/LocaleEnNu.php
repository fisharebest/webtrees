<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnNu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnNu extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryNu;
	}
}
