<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory CR - Costa Rica.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryCr extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'CR';
	}

	public function paperSize() {
		return 'US-Letter';
	}
}
