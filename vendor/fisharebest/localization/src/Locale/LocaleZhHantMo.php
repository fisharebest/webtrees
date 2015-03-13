<?php namespace Fisharebest\Localization;

/**
 * Class LocaleZhHantMo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleZhHantMo extends LocaleZhHant {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryMo;
	}
}
