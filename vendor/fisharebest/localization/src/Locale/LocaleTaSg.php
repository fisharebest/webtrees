<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySg;

/**
 * Class LocaleTaSg
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleTaSg extends LocaleTa {
	protected function digitsGroup() {
		return 3;
	}

	public function territory() {
		return new TerritorySg;
	}
}
