<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory VE - Venezuela.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryVe extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'VE';
	}

	public function firstDay() {
		return 0;
	}

	public function paperSize() {
		return 'US-Letter';
	}
}
