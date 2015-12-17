<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory GL - Greenland.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryGl extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'GL';
	}

	public function firstDay() {
		return 0;
	}
}
