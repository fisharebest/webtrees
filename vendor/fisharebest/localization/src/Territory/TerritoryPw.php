<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory PW - Palau.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryPw extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'PW';
	}

	public function measurementSystem() {
		return 'US';
	}
}
