<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory NG - Nigeria.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryNg extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'NG';
	}

	public function firstDay() {
		return 0;
	}
}
