<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory MZ - Mozambique.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryMz extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'MZ';
	}

	public function firstDay() {
		return 0;
	}
}
