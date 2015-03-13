<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnGu
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnGu extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGu;
	}
}
