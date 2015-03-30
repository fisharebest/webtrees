<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryNr;

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
