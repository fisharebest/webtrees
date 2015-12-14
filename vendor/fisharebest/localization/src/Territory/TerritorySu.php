<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory SU - Union of Soviet Socialist Republics.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritorySu extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'SU';
	}
}
