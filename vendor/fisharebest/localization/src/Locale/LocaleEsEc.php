<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEsEc
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsEc extends LocaleEs {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryEc;
	}
}
