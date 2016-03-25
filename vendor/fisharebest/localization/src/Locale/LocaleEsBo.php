<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBo;

/**
 * Class LocaleEsBo
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleEsBo extends LocaleEs {
	public function territory() {
		return new TerritoryBo;
	}
}
