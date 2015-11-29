<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory AQ - Antarctica.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryAq extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'AQ';
	}
}
