<?php namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of the territory BH - Bahrain.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TerritoryBh extends AbstractTerritory implements TerritoryInterface {
	/** {@inheritdoc} */
	public function code() {
		return 'BH';
	}

	/** {@inheritdoc} */
	public function firstDay() {
		return 6;
	}

	/** {@inheritdoc} */
	public function weekendStart() {
		return 5;
	}

	/** {@inheritdoc} */
	public function weekendEnd() {
		return 6;
	}
}
