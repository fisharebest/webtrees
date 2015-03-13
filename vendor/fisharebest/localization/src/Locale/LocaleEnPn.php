<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnPn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnPn extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryPn;
	}
}
