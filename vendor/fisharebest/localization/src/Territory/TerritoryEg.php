<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory EG - Egypt.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryEg extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'EG';
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
