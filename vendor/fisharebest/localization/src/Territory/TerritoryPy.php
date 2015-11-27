<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory PY - Paraguay.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryPy extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'PY';
	}

	public function firstDay() {
		return 0;
	}
}
