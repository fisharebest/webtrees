<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnGh
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnGh extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGh;
	}
}
