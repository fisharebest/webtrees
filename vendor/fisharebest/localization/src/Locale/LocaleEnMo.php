<?php namespace Fisharebest\Localization;

/**
 * Class LocaleEnMo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEnMo extends LocaleEn {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMo;
	}
}
