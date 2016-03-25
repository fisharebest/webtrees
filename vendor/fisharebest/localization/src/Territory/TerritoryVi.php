<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory VI - U.S. Virgin Islands.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryVi extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'VI';
	}

	public function firstDay() {
		return 0;
	}
}
