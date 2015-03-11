<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnFk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnFk extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryFk;
	}
}
