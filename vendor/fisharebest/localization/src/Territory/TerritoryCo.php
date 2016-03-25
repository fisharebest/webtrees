<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory CO - Colombia.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryCo extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'CO';
	}

	public function firstDay() {
		return 0;
	}

	public function paperSize() {
		return 'US-Letter';
	}
}
