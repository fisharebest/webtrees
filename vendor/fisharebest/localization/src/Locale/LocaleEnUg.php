<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnUg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnUg extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryUg;
	}
}
