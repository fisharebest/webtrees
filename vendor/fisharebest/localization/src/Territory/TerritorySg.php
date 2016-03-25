<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory SG - Singapore.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritorySg extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'SG';
	}

	public function firstDay() {
		return 0;
	}
}
