<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory JP - Japan.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryJp extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'JP';
	}

	public function firstDay() {
		return 0;
	}
}
