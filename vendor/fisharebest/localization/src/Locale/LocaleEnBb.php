<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnBb
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnBb extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryBb;
	}
}
