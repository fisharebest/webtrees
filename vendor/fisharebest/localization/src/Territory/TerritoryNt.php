<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory NT - Neutral Zone.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryNt extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'NT';
	}
}
