<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryEs;

/**
 * Class LocaleEsEs - European Spanish
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsEs extends LocaleEs {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryEs;
	}
}
