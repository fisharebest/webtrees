<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySe;

/**
 * Class LocaleSeSe
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleSeSe extends LocaleSe {
	public function territory() {
		return new TerritorySe;
	}
}
