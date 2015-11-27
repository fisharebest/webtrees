<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory BS - Bahamas.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryBs extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'BS';
	}

	public function firstDay() {
		return 0;
	}

	public function measurementSystem() {
		return 'US';
	}
}
