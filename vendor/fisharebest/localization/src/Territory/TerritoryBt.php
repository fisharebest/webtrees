<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory BT - Bhutan.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryBt extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'BT';
	}

	public function firstDay() {
		return 0;
	}
}
