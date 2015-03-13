<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnJe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnJe extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryJe;
	}
}
