<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory BD - Bangladesh.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryBd extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'BD';
	}

	public function firstDay() {
		return 5;
	}
}
