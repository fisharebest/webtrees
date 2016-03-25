<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory TH - Thailand.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryTh extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'TH';
	}

	public function firstDay() {
		return 0;
	}
}
