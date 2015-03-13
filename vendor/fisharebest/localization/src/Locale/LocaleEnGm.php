<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnGm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnGm extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGm;
	}
}
