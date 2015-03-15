<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnHk
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnHk extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryHk;
	}
}
