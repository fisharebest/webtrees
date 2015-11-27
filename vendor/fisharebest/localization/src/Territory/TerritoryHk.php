<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory HK - Hong Kong.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryHk extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'HK';
	}

	public function firstDay() {
		return 0;
	}
}
