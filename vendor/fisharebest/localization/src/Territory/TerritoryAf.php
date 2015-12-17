<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory AF - Afghanistan.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryAf extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'AF';
	}

	public function firstDay() {
		return 6;
	}

	public function weekendStart() {
		return 4;
	}

	public function weekendEnd() {
		return 5;
	}
}
