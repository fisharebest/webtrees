<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGn;

/**
 * Class LocaleFfGn
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFfGn extends LocaleFf {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryGn;
	}
}
