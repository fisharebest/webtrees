<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory ET - Ethiopia.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryEt extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'ET';
	}

	public function firstDay() {
		return 0;
	}
}
