<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnPr
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnPr extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryPr;
	}
}
