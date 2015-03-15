<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnMt
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnMt extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMt;
	}
}
