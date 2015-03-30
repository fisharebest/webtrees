<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory CL - Chile.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryCl extends AbstractTerritory implements TerritoryInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'CL';
	}

	/** {@inheritdoc} */
	public function paperSize() {
		return 'US-Letter';
	}
}
