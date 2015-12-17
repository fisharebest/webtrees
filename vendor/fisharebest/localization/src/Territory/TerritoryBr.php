<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory BR - Brazil.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryBr extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'BR';
	}

	public function firstDay() {
		return 0;
	}
}
