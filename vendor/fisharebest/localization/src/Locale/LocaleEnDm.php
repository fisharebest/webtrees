<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnDm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnDm extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryDm;
	}
}
