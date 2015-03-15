<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnFj
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnFj extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryFj;
	}
}
