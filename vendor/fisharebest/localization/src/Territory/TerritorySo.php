<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory SO - Somalia.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritorySo extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'SO';
	}

	public function firstDay() {
		return 6;
	}
}
