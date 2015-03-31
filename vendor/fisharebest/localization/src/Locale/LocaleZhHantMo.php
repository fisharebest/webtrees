<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMo;

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
