<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory AR - Argentina.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryAr extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'AR';
	}

	public function firstDay() {
		return 0;
	}
}
