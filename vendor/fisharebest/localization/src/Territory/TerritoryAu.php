<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory AU - Australia.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryAu extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'AU';
	}

	public function firstDay() {
		return 0;
	}
}
