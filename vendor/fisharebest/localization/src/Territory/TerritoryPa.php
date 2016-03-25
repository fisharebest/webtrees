<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory PA - Panama.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryPa extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'PA';
	}

	public function firstDay() {
		return 0;
	}

	public function paperSize() {
		return 'US-Letter';
	}
}
