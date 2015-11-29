<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory BZ - Belize.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryBz extends AbstractTerritory implements TerritoryInterface {
	public function code() {
		return 'BZ';
	}

	public function firstDay() {
		return 0;
	}

	public function measurementSystem() {
		return 'US';
	}

	public function paperSize() {
		return 'US-Letter';
	}
}
