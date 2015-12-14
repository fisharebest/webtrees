<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory NP - Nepal.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryNp extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'NP';
	}

	public function firstDay() {
		return 0;
	}
}
