<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory TN - Tunisia.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryTn extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'TN';
	}

	public function firstDay() {
		return 0;
	}

	public function weekendStart() {
		return 5;
	}

	public function weekendEnd() {
		return 6;
	}
}
