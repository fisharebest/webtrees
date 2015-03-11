<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnIm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnIm extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryIm;
	}
}
