<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory HN - Honduras.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryHn extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'HN';
	}

	public function firstDay() {
		return 0;
	}
}
