<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory BW - Botswana.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryBw extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'BW';
	}

	public function firstDay() {
		return 0;
	}
}
