<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory AS - American Samoa.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryAs extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'AS';
	}

	public function firstDay() {
		return 0;
	}
}
