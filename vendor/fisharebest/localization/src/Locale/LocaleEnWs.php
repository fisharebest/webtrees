<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnWs
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnWs extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryWs;
	}
}
