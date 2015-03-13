<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnBz
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnBz extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryBz;
	}
}
