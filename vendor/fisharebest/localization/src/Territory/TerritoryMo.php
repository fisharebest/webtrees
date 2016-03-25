<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory MO - Macao.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryMo extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'MO';
	}

	public function firstDay() {
		return 0;
	}
}
