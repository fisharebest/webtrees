<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory PR - Puerto Rico.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryPr extends AbstractTerritory implements TerritoryInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'PR';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}

	/** {@inheritdoc} */
	public function measurementSystem() {
		return 'US';
	}

	/** {@inheritdoc} */
	public function paperSize() {
		return 'US-Letter';
	}
}
