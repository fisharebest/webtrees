<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEsIc
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsIc extends LocaleEs {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryIc;
	}
}
