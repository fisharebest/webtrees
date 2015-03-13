<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnNr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnNr extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryNr;
	}
}
