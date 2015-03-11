<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEsAr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsAr extends LocaleEs {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryAr;
	}
}
