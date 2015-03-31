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
	/** {@inheritdoc} */
	public function endonym() {
		return 'français canadien';
	}

	/** {@inheritdoc} */
	public function endonymSortable() {
		return 'FRANCAIS CANADIEN';
	}

	/** {@inheritdoc} */
	public function territory() {
		return new TerritoryCa;
	}
}
