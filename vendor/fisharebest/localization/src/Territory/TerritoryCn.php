<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory CN - China.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryCn extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'CN';
	}

	public function firstDay() {
		return 0;
	}
}
