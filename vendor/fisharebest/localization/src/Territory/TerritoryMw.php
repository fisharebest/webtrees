<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory MW - Malawi.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryMw extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'MW';
	}

	public function firstDay() {
		return 0;
	}
}
