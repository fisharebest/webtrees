<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory MM - Myanmar.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryMm extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'MM';
	}

	public function firstDay() {
		return 0;
	}

	public function measurementSystem() {
		return 'US';
	}
}
