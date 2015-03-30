<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryNe;

/**
 * Class LocaleFrNe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrNe extends LocaleFr {
	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryNe;
	}
}
