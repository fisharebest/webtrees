<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory OM - Oman.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryOm extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'OM';
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
