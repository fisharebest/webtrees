<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory CA - Canada.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryCa extends AbstractTerritory implements TerritoryInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'CA';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 0;
	}

	/** {@inheritdoc} */
	public function paperSize() {
		return 'US-Letter';
	}
}
