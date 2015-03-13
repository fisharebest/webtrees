<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnLr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnLr extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryLr;
	}
}
