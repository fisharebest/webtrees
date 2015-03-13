<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnJm
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnJm extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryJm;
	}
}
