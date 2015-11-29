<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory MT - Malta.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryMt extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'MT';
	}

	public function firstDay() {
		return 0;
	}
}
