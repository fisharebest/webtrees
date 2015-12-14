<?php namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCa;

/**
 * Class LocaleFrCa - Canadian French
 *
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license       GPLv3+
 */
class LocaleFrCa extends LocaleFr {
	public function endonym() {
		return 'français canadien';
	}

	public function endonymSortable() {
		return 'FRANCAIS CANADIEN';
	}

	public function territory() {
		return new TerritoryCa;
	}
}
