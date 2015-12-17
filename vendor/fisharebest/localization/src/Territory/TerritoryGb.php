<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory GB - United Kingdom.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryGb extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'GB';
	}

	public function measurementSystem() {
		return 'UK';
	}
}
