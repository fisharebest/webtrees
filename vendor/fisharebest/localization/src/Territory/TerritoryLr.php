<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory LR - Liberia.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryLr extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'LR';
	}

	public function measurementSystem() {
		return 'US';
	}
}
