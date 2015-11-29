<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryEt;

/**
 * Class LocaleSoEt
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSoEt extends LocaleSo {
	public function territory() {
		return new TerritoryEt;
	}
}
