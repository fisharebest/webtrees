<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory MP - Northern Mariana Islands.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryMp extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'MP';
	}

	public function firstDay() {
		return 0;
	}
}
