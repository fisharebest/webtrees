<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory FK - Falkland Islands (Malvinas).
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryFk extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'FK';
	}
}
