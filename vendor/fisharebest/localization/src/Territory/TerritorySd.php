<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory SD - Sudan.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritorySd extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'SD';
	}

	public function firstDay() {
		return 6;
	}

	public function weekendStart() {
		return 5;
	}

	public function weekendEnd() {
		return 6;
	}
}
