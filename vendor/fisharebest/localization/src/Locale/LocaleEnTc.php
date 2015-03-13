<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnTc
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnTc extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryTc;
	}
}
