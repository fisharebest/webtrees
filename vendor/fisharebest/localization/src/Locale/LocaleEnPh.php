<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnPh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnPh extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryPh;
	}
}
