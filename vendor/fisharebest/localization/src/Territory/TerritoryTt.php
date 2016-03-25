<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory TT - Trinidad and Tobago.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryTt extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'TT';
	}

	public function firstDay() {
		return 0;
	}
}
